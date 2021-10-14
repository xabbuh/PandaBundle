<?php

/*
 * This file is part of the XabbuhPandaBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\PandaBundle\Command;

use Http\Client\HttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xabbuh\PandaClient\Api\AccountManagerInterface;
use Xabbuh\PandaClient\Api\Cloud;
use Xabbuh\PandaClient\Api\HttplugClient;
use Xabbuh\PandaClient\Transformer\TransformerRegistry;

/**
 * Modify a panda cloud via command-line.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class ModifyCloudCommand extends Command
{
    protected static $defaultName = 'panda:cloud:modify';

    private $accountManager;
    private $transformerRegistry;
    private $httpClient;

    public function __construct(AccountManagerInterface $accountManager, TransformerRegistry $transformerRegistry, HttpClient $httpClient = null)
    {
        parent::__construct();

        $this->accountManager = $accountManager;
        $this->transformerRegistry = $transformerRegistry;
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName(static::$defaultName); // BC with Symfony Console 3.3 and older not handling the property automatically
        $this->setDescription('Modify a cloud');
        $this->addOption(
            'account',
            'a',
            InputOption::VALUE_REQUIRED,
            'The account to use to authenticate to the panda service'
        );
        $this->addOption('name', null, InputOption::VALUE_REQUIRED, 'The new cloud name');
        $this->addOption('s3-bucket', null, InputOption::VALUE_REQUIRED, 'The new AWS S3 bucket');
        $this->addOption('access-key', null, InputOption::VALUE_REQUIRED, 'The new access key');
        $this->addOption('secret-key', null, InputOption::VALUE_REQUIRED, 'The new secret key');
        $this->addArgument('cloud-id', InputArgument::REQUIRED, 'The id of the cloud being modified');
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = array();

        // change the cloud name
        if ($name = $input->getOption('name')) {
            $data['name'] = $name;
        }

        // change the s3 bucket
        if ($s3bucket = $input->getOption('s3-bucket')) {
            $data['s3_videos_bucket'] = $s3bucket;
        }

        // change the access key
        if ($accessKey = $input->getOption('access-key')) {
            $data['aws_access_key'] = $accessKey;
        }

        // change the secret key
        if ($secretKey = $input->getOption('secret-key')) {
            $data['aws_secret_key'] = $secretKey;
        }

        // if anything has changed
        if (count($data) > 0) {
            $cloud = $this->getCloud($input->getArgument('cloud-id'), $input->getOption('account'));
            $cloud->setCloud($data, $input->getArgument('cloud-id'));
        }

        return 0;
    }

    private function getCloud(string $cloudId, ?string $accountKey): Cloud
    {
        if (null === $this->accountManager) {
            $this->accountManager = $this->getContainer()->get('xabbuh_panda.account_manager');
        }

        if (null === $this->transformerRegistry) {
            $this->transformerRegistry = $this->getContainer()->get('xabbuh_panda.transformer');
        }

        $account = $this->accountManager->getAccount($accountKey);

        $httpClient = new HttplugClient($this->httpClient);
        $httpClient->setCloudId($cloudId);
        $httpClient->setAccount($account);

        $cloud = new Cloud();
        $cloud->setHttpClient($httpClient);
        $cloud->setTransformers($this->transformerRegistry);

        return $cloud;
    }
}
