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

use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;
use Symfony\Component\Console\Command\Command;
use Xabbuh\PandaBundle\Command\DeleteEncodingCommand;
use Xabbuh\PandaClient\Model\Encoding;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class DeleteEncodingCommandTest extends CloudCommandTest
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        parent::setUp();

        $this->apiMethod = 'deleteEncoding';
    }

    public function testCommand()
    {
        $encodingId = md5(uniqid());
        $encoding = new Encoding();
        $encoding->setId($encodingId);
        $this->defaultCloud
            ->expects($this->once())
            ->method('deleteEncoding')
            ->with($this->equalTo($encoding));
        $this->runCommand('panda:encoding:delete', array('encoding-id' => $encodingId));
    }

    public function testCommandWithoutArguments()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Not enough arguments');

        $this->runCommand('panda:encoding:delete');
    }

    public function testCommandExceptionHandling()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('deleteEncoding')
            ->will($this->throwException($this->createApiException()));
        $this->runCommand(
            'panda:encoding:delete',
            array('encoding-id' => md5(uniqid()))
        );
        $this->assertRegExp('/An error occurred/', $this->commandTester->getDisplay());
    }

    protected function createCommand(): Command
    {
        return new DeleteEncodingCommand($this->cloudManager);
    }

    protected function getDefaultCommandArguments()
    {
        return array('encoding-id' => md5(uniqid()));
    }
}
