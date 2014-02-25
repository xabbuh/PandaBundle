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
use Xabbuh\PandaBundle\Command\UploadVideoCommand;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class UploadVideoCommandTest extends CloudCommandTest
{
    protected function setUp()
    {
        $this->command = new UploadVideoCommand();
        parent::setUp();
    }

    public function testCommand()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('encodeVideoFile')
            ->with('foo');
        $this->runCommand('panda:video:upload', array('filename' => 'foo'));
        $this->assertRegExp('/File uploaded successfully./', $this->commandTester->getDisplay());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testCommandWithoutArguments()
    {
        $this->runCommand('panda:video:upload');
    }
}
