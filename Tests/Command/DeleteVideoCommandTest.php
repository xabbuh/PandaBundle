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
use Xabbuh\PandaBundle\Command\DeleteVideoCommand;
use Xabbuh\PandaClient\Model\Video;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @group legacy
 */
class DeleteVideoCommandTest extends CloudCommandTest
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        parent::setUp();

        $this->apiMethod = 'deleteVideo';
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

    public function testCommandWithoutArguments()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Not enough arguments');

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

    protected function createCommand(): Command
    {
        return new DeleteVideoCommand($this->cloudManager);
    }

    protected function getDefaultCommandArguments()
    {
        return array('video-id' => md5(uniqid()));
    }
}
