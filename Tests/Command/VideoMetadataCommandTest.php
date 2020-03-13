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
use Xabbuh\PandaBundle\Command\VideoMetadataCommand;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class VideoMetadataCommandTest extends CloudCommandTest
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        $this->command = new VideoMetadataCommand();
        $this->apiMethod = 'getVideoMetadata';

        parent::setUp();
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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments
     */
    public function testCommandWithoutArguments()
    {
        $this->runCommand('panda:video:metadata');
    }

    protected function getDefaultCommandArguments()
    {
        return array('video-id' => md5(uniqid()));
    }
}
