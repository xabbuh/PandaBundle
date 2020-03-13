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
use Xabbuh\PandaBundle\Command\ModifyCloudCommand;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @group legacy
 */
class ModifyCloudCommandTest extends CommandTest
{
    use SetUpTearDownTrait;

    /**
     * @var \Xabbuh\PandaBundle\Cloud\CloudFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cloudFactory;

    private function doSetUp()
    {
        $this->command = new ModifyCloudCommand();

        parent::setUp();

        $this->createCloudFactoryMock();
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with('xabbuh_panda.cloud_factory')
            ->will($this->returnValue($this->cloudFactory));
    }

    public function testCommandWithoutOptions()
    {
        $this->cloudFactory
            ->expects($this->never())
            ->method('get');
        $this->runCommand('panda:cloud:modify', array('cloud-id' => md5(uniqid())));
    }

    public function testCommandWithName()
    {
        $cloudId = md5(uniqid());
        $cloud = $this->createCloudMock();
        $this->cloudFactory
            ->expects($this->once())
            ->method('get')
            ->with($cloudId, null)
            ->will($this->returnValue($cloud));
        $cloud->expects($this->once())
            ->method('setCloud')
            ->with($this->equalTo(array('name' => 'foo')), $cloudId);
        $this->runCommand('panda:cloud:modify', array(
            'cloud-id' => $cloudId,
            '--name' => 'foo',
        ));
    }

    public function testCommandWithAccessKeyAndSecretKey()
    {
        $cloudId = md5(uniqid());
        $cloud = $this->createCloudMock();
        $this->cloudFactory
            ->expects($this->once())
            ->method('get')
            ->with($cloudId, null)
            ->will($this->returnValue($cloud));
        $cloud->expects($this->once())
            ->method('setCloud')
            ->with(
                $this->equalTo(array(
                    'aws_access_key' => 'access-key',
                    'aws_secret_key' => 'secret-key',
                )),
                $cloudId
            );
        $this->runCommand('panda:cloud:modify', array(
            'cloud-id' => $cloudId,
            '--access-key' => 'access-key',
            '--secret-key' => 'secret-key',
        ));
    }

    public function testCommandWithAccountAndBucket()
    {
        $cloudId = md5(uniqid());
        $cloud = $this->createCloudMock();
        $this->cloudFactory
            ->expects($this->once())
            ->method('get')
            ->with($cloudId, 'an-account')
            ->will($this->returnValue($cloud));
        $cloud->expects($this->once())
            ->method('setCloud')
            ->with($this->equalTo(array('s3_videos_bucket' => 'foo')), $cloudId);
        $this->runCommand('panda:cloud:modify', array(
            'cloud-id' => $cloudId,
            '--account' => 'an-account',
            '--s3-bucket' => 'foo',
        ));
    }

    public function testCommandWithoutArguments()
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Not enough arguments');

        $this->runCommand('panda:cloud:modify');
    }

    private function createCloudFactoryMock()
    {
        $this->cloudFactory = $this->getMockBuilder('\Xabbuh\PandaBundle\Cloud\CloudFactory')
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();
    }

    private function createCloudMock()
    {
        return $this->getMockBuilder('\Xabbuh\PandaClient\Api\Cloud')->getMock();
    }
}
