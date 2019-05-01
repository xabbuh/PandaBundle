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

use Xabbuh\PandaBundle\Command\ProfileInfoCommand;
use Xabbuh\PandaClient\Model\Profile;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ProfileInfoCommandTest extends CloudCommandTest
{
    protected function setUp()
    {
        parent::setUp();

        $this->command = new ProfileInfoCommand($this->cloudManager);
        $this->apiMethod = 'getProfile';
    }

    public function testCommand()
    {
        $profileId = md5(uniqid());
        $profile = new Profile();
        $profile->setId($profileId);
        $profile->setTitle('MP4 (H.264)');
        $profile->setName('h264');
        $profile->setExtname('.mp4');
        $profile->setWidth(800);
        $profile->setHeight(600);
        $profile->setAspectMode('letterbox');
        $profile->setCreatedAt('2012/09/04 13:13:55 +0000');
        $profile->setUpdatedAt('2013/01/22 22:36:53 +0000');
        $this->defaultCloud
            ->expects($this->once())
            ->method('getProfile')
            ->will($this->returnValue($profile));
        $this->runCommand('panda:profile:info', array('profile-id' => $profileId));
        $this->validateTableRows(array(
            array('id', $profileId),
            array('title', 'MP4 (H.264)'),
            array('name', 'h264'),
            array('file extension', '.mp4'),
            array('width', '800'),
            array('height', '600'),
            array('aspect mode', 'letterbox'),
            array('created at', '2012/09/04 13:13:55 +0000'),
            array('updated at', '2013/01/22 22:36:53 +0000'),
        ));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments
     */
    public function testCommandWithoutArguments()
    {
        $this->runCommand('panda:profile:info');
    }

    protected function getDefaultCommandArguments()
    {
        return array('profile-id' => md5(uniqid()));
    }
}
