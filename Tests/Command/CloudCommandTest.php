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

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class CloudCommandTest extends CommandTest
{
    /**
     * @var \Xabbuh\PandaClient\Api\CloudManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cloudManager;

    /**
     * @var \Xabbuh\PandaClient\Api\Cloud|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $defaultCloud;

    protected function createContainerMock()
    {
        parent::createContainerMock();

        $this->createCloudManagerMock();
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with('xabbuh_panda.cloud_manager')
            ->will($this->returnValue($this->cloudManager));
    }

    protected function createCloudManagerMock()
    {
        $this->createDefaultCloudMock();
        $this->cloudManager = $this->getMock('\Xabbuh\PandaClient\Api\CloudManager');
        $this->cloudManager
            ->expects($this->any())
            ->method('getDefaultCloud')
            ->will($this->returnValue($this->defaultCloud));
    }

    protected function createDefaultCloudMock()
    {
        $this->defaultCloud = $this->getMock('\Xabbuh\PandaClient\Api\Cloud');
    }

    protected function validateTableRow($label, $value)
    {
        $regex = sprintf(
            '/%s\s*\|\s*%s/',
            preg_quote($label, '/'),
            preg_quote($value, '/')
        );
        $this->assertRegExp($regex, $this->commandTester->getDisplay());
    }

    protected function validateTableRows($rows)
    {
        foreach ($rows as $row) {
            $this->validateTableRow($row[0], $row[1]);
        }
    }
}
