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
use Xabbuh\PandaBundle\Command\VideoMetadataCommand;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class VideoMetadataCommandTest extends CloudCommandTest
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        parent::setUp();

        $this->apiMethod = 'getVideoMetadata';
    }

    public function testCommand()
    {
        $metadata = array(
            'foo' => 'bar',
            'list' => array('foobar', 'baz'),
        );
        $videoId = md5(uniqid());
        $this->defaultCloud
            ->expects($this->once())
            ->method('getVideoMetadata')
            ->will($this->returnValue($metadata));
        $this->runCommand(
            'panda:video:metadata',
            array('video-id' => $videoId)
        );
        $this->validateTableRows(array(
            array('foo', 'bar'),
            array('list', 'foobar, baz'),
        ));
    }

    public function testCommandWithoutArguments()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Not enough arguments');

        $this->runCommand('panda:video:metadata');
    }

    protected function createCommand(): Command
    {
        return new VideoMetadataCommand($this->cloudManager);
    }

    protected function getDefaultCommandArguments()
    {
        return array('video-id' => md5(uniqid()));
    }
}
