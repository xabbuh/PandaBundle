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
        parent::setUp();

        $this->command = new UploadVideoCommand($this->cloudManager);
        $this->apiMethod = 'encodeVideoFile';
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

    public function testCommandWithOneProfileOption()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('encodeVideoFile')
            ->with('foo', array('profile1'));
        $this->runCommand('panda:video:upload', array(
            '--profile' => array('profile1'),
            'filename' => 'foo',
        ));
        $this->assertRegExp('/File uploaded successfully./', $this->commandTester->getDisplay());
    }

    public function testCommandWithMultipleProfileOptions()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('encodeVideoFile')
            ->with('foo', array('profile1', 'profile2'));
        $this->runCommand('panda:video:upload', array(
            '--profile' => array('profile1', 'profile2'),
            'filename' => 'foo',
        ));
        $this->assertRegExp('/File uploaded successfully./', $this->commandTester->getDisplay());
    }

    public function testCommandWithCustomPathFormatOption()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('encodeVideoFile')
            ->with('foo', array(), 'bar/:id', null);
        $this->runCommand('panda:video:upload', array(
            '--path-format' => 'bar/:id',
            'filename' => 'foo',
        ));
        $this->assertRegExp('/File uploaded successfully./', $this->commandTester->getDisplay());
    }

    public function testCommandWithPayloadOption()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('encodeVideoFile')
            ->with('foo', array(), null, 'baz');
        $this->runCommand('panda:video:upload', array(
            '--payload' => 'baz',
            'filename' => 'foo',
        ));
        $this->assertRegExp('/File uploaded successfully./', $this->commandTester->getDisplay());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments
     */
    public function testCommandWithoutArguments()
    {
        $this->runCommand('panda:video:upload');
    }

    protected function getDefaultCommandArguments()
    {
        return array('filename' => 'video-file.mp4');
    }
}
