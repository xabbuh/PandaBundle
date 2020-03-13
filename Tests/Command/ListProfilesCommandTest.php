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
use Xabbuh\PandaBundle\Command\ListProfilesCommand;
use Xabbuh\PandaClient\Model\Profile;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ListProfilesCommandTest extends CloudCommandTest
{
    use SetUpTearDownTrait;

    private function doSetUp()
    {
        $this->command = new ListProfilesCommand();
        $this->apiMethod = 'getProfiles';

        parent::setUp();
    }

    public function testCommand()
    {
        $profileId1 = md5(uniqid());
        $profile1 = new Profile();
        $profile1->setId($profileId1);
        $profile1->setTitle('Profile 1');
        $profileId2 = md5(uniqid());
        $profile2 = new Profile();
        $profile2->setId($profileId2);
        $profile2->setTitle('Profile 2');
        $this->defaultCloud
            ->expects($this->once())
            ->method('getProfiles')
            ->will($this->returnValue(array($profile1, $profile2)));
        $this->runCommand('panda:profile:list');
        $this->validateTableRows(array(
            array($profileId1, 'Profile 1'),
            array($profileId2, 'Profile 2'),
        ));
    }
}
