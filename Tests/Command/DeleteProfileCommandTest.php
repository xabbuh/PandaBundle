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
use Xabbuh\PandaBundle\Command\DeleteProfileCommand;
use Xabbuh\PandaClient\Model\Profile;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class DeleteProfileCommandTest extends CloudCommandTest
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        $this->command = new DeleteProfileCommand();
        $this->apiMethod = 'deleteProfile';

        parent::setUp();
    }

    public function testCommand()
    {
        $profileId = md5(uniqid());
        $profile = new Profile();
        $profile->setId($profileId);
        $this->defaultCloud
            ->expects($this->once())
            ->method('deleteProfile')
            ->with($this->equalTo($profile));
        $this->runCommand('panda:profile:delete', array('profile-id' => $profileId));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments
     */
    public function testCommandWithoutArguments()
    {
        $this->runCommand('panda:profile:delete');
    }

    public function testCommandExceptionHandling()
    {
        $this->defaultCloud
            ->expects($this->once())
            ->method('deleteProfile')
            ->will($this->throwException($this->createApiException()));
        $this->runCommand(
            'panda:profile:delete',
            array('profile-id' => md5(uniqid()))
        );
        $this->assertRegExp('/An error occurred/', $this->commandTester->getDisplay());
    }

    protected function getDefaultCommandArguments()
    {
        return array('profile-id' => md5(uniqid()));
    }
}
