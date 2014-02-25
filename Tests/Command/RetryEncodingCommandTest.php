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

use Xabbuh\PandaBundle\Command\RetryEncodingCommand;
use Xabbuh\PandaClient\Model\Encoding;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class RetryEncodingCommandTest extends CloudCommandTest
{
    protected function setUp()
    {
        $this->command = new RetryEncodingCommand();

        parent::setUp();
    }

    public function testCommand()
    {
        $encodingId = md5(uniqid());
        $encoding = new Encoding();
        $encoding->setId($encodingId);
        $this->defaultCloud
            ->expects($this->once())
            ->method('retryEncoding')
            ->with($this->equalTo($encoding));
        $this->runCommand('panda:encoding:retry', array('encoding-id' => $encodingId));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testCommandWithoutArguments()
    {
        $this->runCommand('panda:encoding:retry');
    }

    public function testCommandExceptionHandling()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('retryEncoding')
            ->will($this->throwException($this->createApiException()));
        $this->runCommand(
            'panda:encoding:retry',
            array('encoding-id' => md5(uniqid()))
        );
        $this->assertRegExp('/An error occurred/', $this->commandTester->getDisplay());
    }
}
