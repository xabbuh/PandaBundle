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

use Xabbuh\PandaBundle\Command\CreateEncodingCommand;
use Xabbuh\PandaClient\Model\Encoding;
use Xabbuh\PandaClient\Model\Video;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CreateEncodingCommandTest extends CloudCommandTest
{
    protected function setUp()
    {
        parent::setUp();

        $this->command = new CreateEncodingCommand($this->cloudManager);
        $this->apiMethod = 'createEncodingWithProfileId';
    }

    public function testCommandWithProfileId()
    {
        $videoId = md5(uniqid());
        $video = new Video();
        $video->setId($videoId);
        $profileId = md5(uniqid());
        $encodingId = md5(uniqid());
        $encoding = new Encoding();
        $encoding->setId($encodingId);
        $this->defaultCloud
            ->expects($this->once())
            ->method('createEncodingWithProfileId')
            ->with($this->equalTo($video), $profileId)
            ->will($this->returnValue($encoding));
        $this->runCommand('panda:encoding:create', array(
            'video-id' => $videoId,
            '--profile-id' => $profileId,
        ));
        $this->assertRegExp(
            '/Successfully created encoding with id '.$encodingId.'/',
            $this->commandTester->getDisplay()
        );
    }

    public function testCommandWithProfileName()
    {
        $videoId = md5(uniqid());
        $video = new Video();
        $video->setId($videoId);
        $encodingId = md5(uniqid());
        $encoding = new Encoding();
        $encoding->setId($encodingId);
        $this->defaultCloud
            ->expects($this->once())
            ->method('createEncodingWithProfileName')
            ->with($this->equalTo($video), 'h264')
            ->will($this->returnValue($encoding));
        $this->runCommand('panda:encoding:create', array(
            'video-id' => $videoId,
            '--profile-name' => 'h264',
        ));
        $this->assertRegExp(
            '/Successfully created encoding with id '.$encodingId.'/',
            $this->commandTester->getDisplay()
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments
     */
    public function testCommandWithoutArguments()
    {
        $this->runCommand('panda:encoding:create');
    }

    public function testCommandWithoutOptions()
    {
        $this->runCommand('panda:encoding:create', array('video-id' => md5(uniqid())));
        $this->assertRegExp(
            '/Exactly one option of --profile-id or --profile-name must be given./',
            $this->commandTester->getDisplay()
        );
    }

    public function testCommandWithProfileIdAndProfileName()
    {
        $this->runCommand('panda:encoding:create', array(
            'video-id' => md5(uniqid()),
            '--profile-id' => md5(uniqid()),
            '--profile-name' => 'h264',
        ));
        $this->assertRegExp(
            '/Exactly one option of --profile-id or --profile-name must be given./',
            $this->commandTester->getDisplay()
        );
    }

    public function testCommandExceptionHandling()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('createEncodingWithProfileId')
            ->will($this->throwException($this->createApiException()));
        $this->runCommand(
            'panda:encoding:create',
            array(
                '--profile-id' => md5(uniqid()),
                'video-id' => md5(uniqid()),
            )
        );
        $this->assertRegExp('/An error occurred/', $this->commandTester->getDisplay());
    }

    protected function getDefaultCommandArguments()
    {
        return array('--profile-id' => md5(uniqid()), 'video-id' => md5(uniqid()));
    }
}
