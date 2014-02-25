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

use Xabbuh\PandaBundle\Command\DeleteVideoCommand;
use Xabbuh\PandaClient\Model\Video;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class DeleteVideoCommandTest extends CloudCommandTest
{
    protected function setUp()
    {
        $this->command = new DeleteVideoCommand();

        parent::setUp();
    }

    public function testCommand()
    {
        $videoId = md5(uniqid());
        $video = new Video();
        $video->setId($videoId);
        $this->defaultCloud
            ->expects($this->once())
            ->method('deleteVideo')
            ->with($this->equalTo($video));
        $this->runCommand('panda:video:delete', array('video-id' => $videoId));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testCommandWithoutArguments()
    {
        $this->runCommand('panda:video:delete');
    }

    public function testCommandExceptionHandling()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('deleteVideo')
            ->will($this->throwException($this->createApiException()));
        $this->runCommand(
            'panda:video:delete',
            array('video-id' => md5(uniqid()))
        );
        $this->assertRegExp('/An error occurred/', $this->commandTester->getDisplay());
    }
}
