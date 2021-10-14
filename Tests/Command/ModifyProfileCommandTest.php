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

use Symfony\Component\Console\Command\Command;
use Xabbuh\PandaBundle\Command\ModifyProfileCommand;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ModifyProfileCommandTest extends CloudCommandTest
{
    /**
     * @var \Xabbuh\PandaClient\Model\Profile&\PHPUnit\Framework\MockObject\MockObject
     */
    private $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiMethod = 'getProfile';

        $this->profile = $this->createProfileMock();
        $this->defaultCloud
            ->expects($this->any())
            ->method('getProfile')
            ->will($this->returnValue($this->profile));
    }

    public function testCommandWithoutOptions()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('setProfile')
            ->with($this->profile);
        $this->runCommand('panda:profile:modify', array('profile-id' => md5(uniqid())));
    }

    public function testCommandWithNameAndTitle()
    {
        $this->profile
            ->expects($this->once())
            ->method('setName')
            ->with('h264');
        $this->profile
            ->expects($this->once())
            ->method('setTitle')
            ->with('MP4 (H.264)');
        $this->defaultCloud
            ->expects($this->once())
            ->method('setProfile')
            ->with($this->equalTo($this->profile));
        $this->runCommand('panda:profile:modify', array(
            'profile-id' => 'cloud-id',
            '--name' => 'h264',
            '--title' => 'MP4 (H.264)',
        ));
        $this->assertRegExp(
            '/Successfully modified profile/',
            $this->commandTester->getDisplay()
        );
    }

    public function testCommandWithVideoOptions()
    {
        $this->profile
            ->expects($this->once())
            ->method('setExtname')
            ->with('.mp4');
        $this->profile
            ->expects($this->once())
            ->method('setWidth')
            ->with(800);
        $this->profile
            ->expects($this->once())
            ->method('setHeight')
            ->with(600);
        $this->profile
            ->expects($this->once())
            ->method('setAudioBitrate')
            ->with(48);
        $this->profile
            ->expects($this->once())
            ->method('setVideoBitrate')
            ->with(96);
        $this->profile
            ->expects($this->once())
            ->method('setAspectMode')
            ->with('letterbox');
        $this->defaultCloud
            ->expects($this->once())
            ->method('setProfile')
            ->with($this->equalTo($this->profile));
        $this->runCommand('panda:profile:modify', array(
            'profile-id' => 'cloud-id',
            '--extname' => '.mp4',
            '--width' => 800,
            '--height' => 600,
            '--audio-bitrate' => 48,
            '--video-bitrate' => 96,
            '--aspect-mode' => 'letterbox',
        ));
        $this->assertRegExp(
            '/Successfully modified profile/',
            $this->commandTester->getDisplay()
        );
    }

    public function testCommandWithoutArguments()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Not enough arguments');

        $this->runCommand('panda:profile:modify');
    }

    public function testCommandExceptionHandling()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('setProfile')
            ->will($this->throwException($this->createApiException()));
        $this->runCommand(
            'panda:profile:modify',
            array('profile-id' => md5(uniqid()))
        );
        $this->assertRegExp('/An error occurred/', $this->commandTester->getDisplay());
    }

    protected function createCommand(): Command
    {
        return new ModifyProfileCommand($this->cloudManager);
    }

    protected function getDefaultCommandArguments()
    {
        return array('profile-id' => md5(uniqid()));
    }

    private function createProfileMock()
    {
        return $this->getMockBuilder('\Xabbuh\PandaClient\Model\Profile')->getMock();
    }
}
