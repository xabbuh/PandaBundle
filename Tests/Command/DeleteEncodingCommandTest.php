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

use Xabbuh\PandaBundle\Command\DeleteEncodingCommand;
use Xabbuh\PandaClient\Model\Encoding;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class DeleteEncodingCommandTest extends CloudCommandTest
{
    protected function setUp()
    {
        $this->command = new DeleteEncodingCommand();
        $this->apiMethod = 'deleteEncoding';

        parent::setUp();
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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testCommandWithoutArguments()
    {
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

    protected function getDefaultCommandArguments()
    {
        return array('encoding-id' => md5(uniqid()));
    }
}
