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

use Xabbuh\PandaBundle\Command\EncodingInfoCommand;
use Xabbuh\PandaClient\Model\Encoding;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class EncodingInfoCommandTest extends CloudCommandTest
{
    protected function setUp()
    {
        $this->command = new EncodingInfoCommand();

        parent::setUp();
    }

    public function testCommand()
    {
        $encodingId = md5(uniqid());
        $videoId = md5(uniqid());
        $profileId = md5(uniqid());
        $encoding = new Encoding();
        $encoding->setId($encodingId);
        $encoding->setVideoId($videoId);
        $encoding->setExtname('.mp4');
        $encoding->setProfileId($profileId);
        $encoding->setProfileName('h264');
        $encoding->setWidth(800);
        $encoding->setHeight(600);
        $encoding->setStatus('success');
        $encoding->setEncodingProgress(96);
        $encoding->setEncodingTime(185);
        $encoding->setCreatedAt('2012/09/04 13:13:55 +0000');
        $encoding->setUpdatedAt('2013/01/22 22:36:53 +0000');
        $this->defaultCloud
            ->expects($this->once())
            ->method('getEncoding')
            ->will($this->returnValue($encoding));
        $this->runCommand('panda:encoding:info', array('encoding-id' => $encodingId));
        $this->validateTableRows(array(
            array('id', $encodingId),
            array('video id', $videoId),
            array('file extension', '.mp4'),
            array('profile id', $profileId),
            array('profile name', 'h264'),
            array('width', '800'),
            array('height', '600'),
            array('status', 'success'),
            array('encoding progress', '96'),
            array('encoding time', '185'),
            array('created at', '2012/09/04 13:13:55 +0000'),
            array('updated at', '2013/01/22 22:36:53 +0000'),
        ));
    }

    public function testCommandWithFailedEncoding()
    {
        $encodingId = md5(uniqid());
        $videoId = md5(uniqid());
        $profileId = md5(uniqid());
        $encoding = new Encoding();
        $encoding->setId($encodingId);
        $encoding->setVideoId($videoId);
        $encoding->setExtname('.mp4');
        $encoding->setProfileId($profileId);
        $encoding->setProfileName('h264');
        $encoding->setWidth(800);
        $encoding->setHeight(600);
        $encoding->setStatus('success');
        $encoding->setEncodingProgress(96);
        $encoding->setEncodingTime(185);
        $encoding->setStatus('fail');
        $encoding->setErrorMessage('Encoding failed');
        $encoding->setCreatedAt('2012/09/04 13:13:55 +0000');
        $encoding->setUpdatedAt('2013/01/22 22:36:53 +0000');
        $this->defaultCloud
            ->expects($this->once())
            ->method('getEncoding')
            ->with($encodingId)
            ->will($this->returnValue($encoding));
        $this->runCommand('panda:encoding:info', array('encoding-id' => $encodingId));
        $this->validateTableRows(array(
            array('id', $encodingId),
            array('video id', $videoId),
            array('file extension', '.mp4'),
            array('profile id', $profileId),
            array('profile name', 'h264'),
            array('width', '800'),
            array('height', '600'),
            array('status', 'fail'),
            array('encoding progress', '96'),
            array('encoding time', '185'),
            array('error message', 'Encoding failed'),
            array('created at', '2012/09/04 13:13:55 +0000'),
            array('updated at', '2013/01/22 22:36:53 +0000'),
        ));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testCommandWithoutArguments()
    {
        $this->runCommand('panda:encoding:info');
    }
}
