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
use Xabbuh\PandaBundle\Command\ListEncodingsCommand;
use Xabbuh\PandaClient\Model\Encoding;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ListEncodingsCommandTest extends CloudCommandTest
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        parent::setUp();

        $this->apiMethod = 'getEncodings';
    }

    public function testCommandWithoutOptions()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('getEncodings')
            ->with(array())
            ->will($this->returnValue($this->createEncodingResult()));
        $this->runCommand('panda:encoding:list');
        $this->validateEncodingResultOutput();
    }

    public function testCommandWithStatus()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('getEncodings')
            ->with(array('status' => 'success'))
            ->will($this->returnValue($this->createEncodingResult()));
        $this->runCommand('panda:encoding:list', array('--status' => 'success'));
        $this->validateEncodingResultOutput();
    }

    public function testCommandWithProfileId()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('getEncodings')
            ->with(array('profile_id' => 'profile-id'))
            ->will($this->returnValue($this->createEncodingResult()));
        $this->runCommand('panda:encoding:list', array('--profile-id' => 'profile-id'));
        $this->validateEncodingResultOutput();
    }

    public function testCommandWithProfileName()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('getEncodings')
            ->with(array('profile_name' => 'profile-name'))
            ->will($this->returnValue($this->createEncodingResult()));
        $this->runCommand('panda:encoding:list', array('--profile-name' => 'profile-name'));
        $this->validateEncodingResultOutput();
    }

    public function testCommandWithVideoId()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('getEncodings')
            ->with(array('video_id' => 'video-id'))
            ->will($this->returnValue($this->createEncodingResult()));
        $this->runCommand('panda:encoding:list', array('--video-id' => 'video-id'));
        $this->validateEncodingResultOutput();
    }

    protected function createCommand(): Command
    {
        return new ListEncodingsCommand($this->cloudManager);
    }

    private function createEncodingResult()
    {
        $encoding1 = new Encoding();
        $encoding1->setId('encoding-1');
        $encoding1->setStatus('processing');
        $encoding2 = new Encoding();
        $encoding2->setId('encoding-2');
        $encoding2->setStatus('success');
        $encoding3 = new Encoding();
        $encoding3->setId('encoding-3');
        $encoding3->setStatus('fail');

        return array($encoding1, $encoding2, $encoding3);
    }

    private function validateEncodingResultOutput()
    {
        $this->assertRegExp(
            '/encoding-1\s*\|.*\|\s*processing/',
            $this->commandTester->getDisplay()
        );
        $this->assertRegExp(
            '/encoding-2\s*\|.*\|\s*success/',
            $this->commandTester->getDisplay()
        );
        $this->assertRegExp(
            '/encoding-3\s*\|.*\|\s*fail/',
            $this->commandTester->getDisplay()
        );
    }
}
