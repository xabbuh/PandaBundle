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
use Xabbuh\PandaBundle\Command\UploadVideoCommand;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @group legacy
 */
class UploadVideoCommandTest extends CloudCommandTest
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        parent::setUp();

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

    public function testCommandWithoutArguments()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Not enough arguments');

        $this->runCommand('panda:video:upload');
    }

    protected function createCommand(): Command
    {
        return new UploadVideoCommand($this->cloudManager);
    }

    protected function getDefaultCommandArguments()
    {
        return array('filename' => 'video-file.mp4');
    }
}
