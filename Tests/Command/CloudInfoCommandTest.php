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

use Symfony\Component\Console\Command\Command;
use Xabbuh\PandaBundle\Command\CloudInfoCommand;
use Xabbuh\PandaClient\Model\Cloud;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CloudInfoCommandTest extends CloudCommandTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->apiMethod = 'getCloud';
    }

    public function testCommand()
    {
        $cloudId = md5(uniqid());
        $cloud = new Cloud();
        $cloud->setId($cloudId);
        $cloud->setName('foo');
        $cloud->setCreatedAt('2012/09/04 13:13:55 +0000');
        $cloud->setUpdatedAt('2013/01/22 22:36:53 +0000');
        $this->defaultCloud
            ->expects($this->once())
            ->method('getCloud')
            ->will($this->returnValue($cloud));
        $this->runCommand('panda:cloud:info');
        $this->validateTableRows(array(
            array('id', $cloudId),
            array('name', 'foo'),
            array('created at', '2012/09/04 13:13:55 +0000'),
            array('updated at', '2013/01/22 22:36:53 +0000'),
        ));
    }

    protected function createCommand(): Command
    {
        return new CloudInfoCommand($this->cloudManager);
    }
}
