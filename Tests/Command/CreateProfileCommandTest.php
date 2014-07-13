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

use Xabbuh\PandaBundle\Command\CreateProfileCommand;
use Xabbuh\PandaClient\Model\Profile;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CreateProfileCommandTest extends CloudCommandTest
{
    protected function setUp()
    {
        $this->command = new CreateProfileCommand();
        $this->apiMethod = 'addProfileFromPreset';

        parent::setUp();
    }

    public function testCommand()
    {
        $profileId = md5(uniqid());
        $profile = new Profile();
        $profile->setId($profileId);
        $profile->setName('h264');
        $this->defaultCloud
            ->expects($this->once())
            ->method('addProfileFromPreset')
            ->with('h264')
            ->will($this->returnValue($profile));
        $this->runCommand(
            'panda:profile:create',
            array('preset' => 'h264')
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testCommandWithoutArguments()
    {
        $this->runCommand('panda:profile:create');
    }

    public function testCommandExceptionHandling()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('addProfileFromPreset')
            ->will($this->throwException($this->createApiException()));
        $this->runCommand(
            'panda:profile:create',
            array('preset' => 'h264')
        );
        $this->assertRegExp('/An error occurred/', $this->commandTester->getDisplay());
    }

    protected function getDefaultCommandArguments()
    {
        return array('preset' => 'h264');
    }
}
