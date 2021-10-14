<?php

/*
 * This file is part of the XabbuhPandaBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\PandaBundle\Tests\Command;

use Http\Mock\Client;
use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Console\Command\Command;
use Xabbuh\PandaBundle\Command\ModifyCloudCommand;
use Xabbuh\PandaClient\Api\Account;
use Xabbuh\PandaClient\Api\AccountManager;
use Xabbuh\PandaClient\Serializer\Symfony\Serializer;
use Xabbuh\PandaClient\Transformer\CloudTransformer;
use Xabbuh\PandaClient\Transformer\TransformerRegistry;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ModifyCloudCommandTest extends CommandTest
{
    /**
     * @var Client
     */
    private $httpClient;

    public function testCommandWithoutOptions()
    {
        $this->runCommand('panda:cloud:modify', array('cloud-id' => md5(uniqid())));

        $this->assertCount(0, $this->httpClient->getRequests());
    }

    public function testCommandWithName()
    {
        $cloudId = md5(uniqid());

        $this->httpClient->addResponse(new Response(200, [], json_encode(['name' => 'foo'])));

        $this->runCommand('panda:cloud:modify', array(
            'cloud-id' => $cloudId,
            '--name' => 'foo',
        ));

        $lastRequest = $this->httpClient->getLastRequest();

        $this->assertCount(1, $this->httpClient->getRequests());
        $this->assertSame(sprintf('https://api.pandastream.com/v2/clouds/%s.json', $cloudId), (string) $lastRequest->getUri());
        $this->assertCloudDataHaveBeenSent($lastRequest, [
            'access_key' => 'default account access key',
            'cloud_id' => $cloudId,
            'name' => 'foo',
        ]);
    }

    public function testCommandWithAccessKeyAndSecretKey()
    {
        $cloudId = md5(uniqid());

        $this->httpClient->addResponse(new Response(200, [], json_encode(['name' => 'foo'])));

        $this->runCommand('panda:cloud:modify', array(
            'cloud-id' => $cloudId,
            '--access-key' => 'access-key',
            '--secret-key' => 'secret-key',
        ));

        $lastRequest = $this->httpClient->getLastRequest();

        $this->assertCount(1, $this->httpClient->getRequests());
        $this->assertSame(sprintf('https://api.pandastream.com/v2/clouds/%s.json', $cloudId), (string) $lastRequest->getUri());
        $this->assertCloudDataHaveBeenSent($lastRequest, [
            'access_key' => 'default account access key',
            'aws_access_key' => 'access-key',
            'aws_secret_key' => 'secret-key',
            'cloud_id' => $cloudId,
        ]);
    }

    public function testCommandWithAccountAndBucket()
    {
        $cloudId = md5(uniqid());

        $this->httpClient->addResponse(new Response(200, [], json_encode(['name' => 'foo'])));

        $this->runCommand('panda:cloud:modify', array(
            'cloud-id' => $cloudId,
            '--account' => 'an-account',
            '--s3-bucket' => 'foo',
        ));

        $lastRequest = $this->httpClient->getLastRequest();

        $this->assertCount(1, $this->httpClient->getRequests());
        $this->assertSame(sprintf('https://api.pandastream.com/v2/clouds/%s.json', $cloudId), (string) $lastRequest->getUri());
        $this->assertCloudDataHaveBeenSent($lastRequest, [
            'access_key' => 'second account access key',
            'cloud_id' => $cloudId,
            's3_videos_bucket' => 'foo',
        ]);
    }

    public function testCommandWithoutArguments()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Not enough arguments');

        $this->runCommand('panda:cloud:modify');
    }

    protected function createCommand(): Command
    {
        $accountManager = new AccountManager();
        $accountManager->registerAccount('default-account', new Account('default account access key', 'default account secret key', 'api.pandastream.com'));
        $accountManager->registerAccount('an-account', new Account('second account access key', 'second account secret key', 'api.pandastream.com'));
        $accountManager->setDefaultAccount('default-account');

        $cloudTransformer = new CloudTransformer();
        $cloudTransformer->setSerializer(new Serializer());
        $transformerRegistry = new TransformerRegistry();
        $transformerRegistry->setCloudTransformer($cloudTransformer);

        $this->httpClient = new Client();

        return new ModifyCloudCommand($accountManager, $transformerRegistry, $this->httpClient);
    }

    private function assertCloudDataHaveBeenSent(RequestInterface $request, array $expectedData): void
    {
        parse_str((string) $request->getBody(), $sentData);

        unset($sentData['signature']);
        unset($sentData['timestamp']);

        $this->assertEquals($expectedData, $sentData);
    }
}
