<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Xabbuh\PandaBundle\DependencyInjection\ProviderCompilerPass;

/**
 * Test the compiler pass that registers services as account or cloud
 * providers if they are tagged as such.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ProviderCompilerClassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\Definition
     */
    protected $accountManagerDefinitionMock;

    /**
     * @var \Symfony\Component\DependencyInjection\Definition
     */
    protected $cloudManagerDefinitionMock;

    /**
     * @var \Symfony\Component\DependencyInjection\Definition
     */
    protected $containerBuilderMock;


    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->accountManagerDefinitionMock = $this->getMockBuilder("\\Symfony\\Component\\DependencyInjection\\Definition")
            ->getMock();
        $this->cloudManagerDefinitionMock = $this->getMockBuilder("\\Symfony\\Component\\DependencyInjection\\Definition")
            ->getMock();

        $this->containerBuilderMock = $this->getMock("Symfony\\Component\\DependencyInjection\\ContainerBuilder");
        $this->containerBuilderMock->expects($this->any())
            ->method("hasDefinition")
            ->will($this->returnValue(true));
        $returnValueMap = array(
            array("xabbuh_panda.account_manager", $this->accountManagerDefinitionMock),
            array("xabbuh_panda.cloud_manager", $this->cloudManagerDefinitionMock)
        );
        $this->containerBuilderMock->expects($this->any())
            ->method("getDefinition")
            ->will($this->returnValueMap($returnValueMap));
    }

    /**
     * Tests that no register method is called when no services are tagged
     * as providers.
     */
    public function testWithoutProviders()
    {
        $this->accountManagerDefinitionMock->expects($this->never())
            ->method("addMethodCall");
        $this->cloudManagerDefinitionMock->expects($this->never())
            ->method("addMethodCall");
        $this->containerBuilderMock->expects($this->any())
            ->method("findTaggedServiceIds")
            ->will($this->returnValue(array()));
        $pass = new ProviderCompilerPass();
        $pass->process($this->containerBuilderMock);
    }

    /**
     * Ensure that registerProvider on AccountManager is called once whereas
     * registerProvider on CloudManager is called never when one account
     * provider and no cloud provider is registered.
     */
    public function testWithOneAccountProvider()
    {
        $this->accountManagerDefinitionMock->expects($this->once())
            ->method("addMethodCall")
            ->with($this->equalTo("registerProvider"));
        $this->cloudManagerDefinitionMock->expects($this->never())
            ->method("addMethodCall");
        $returnValueMap = array(
            array("xabbuh_panda.account_provider", array("id" => array())),
            array("xabbuh_panda.cloud_provider", array())
        );
        $this->containerBuilderMock->expects($this->any())
            ->method("findTaggedServiceIds")
            ->will($this->returnValueMap($returnValueMap));
        $pass = new ProviderCompilerPass();
        $pass->process($this->containerBuilderMock);
    }

    /**
     * Ensure that registerProvider on AccountManager is called twice whereas
     * registerProvider on CloudManager is called never when two account
     * providers and no cloud provider is registered.
     */
    public function testWithTwoAccountProviders()
    {
        $this->accountManagerDefinitionMock->expects($this->exactly(2))
            ->method("addMethodCall")
            ->with($this->equalTo("registerProvider"));
        $this->cloudManagerDefinitionMock->expects($this->never())
            ->method("addMethodCall");
        $returnValueMap = array(
            array(
                "xabbuh_panda.account_provider",
                array("id1" => array(), "id2" => array())
            ),
            array("xabbuh_panda.cloud_provider", array())
        );
        $this->containerBuilderMock->expects($this->any())
            ->method("findTaggedServiceIds")
            ->will($this->returnValueMap($returnValueMap));
        $pass = new ProviderCompilerPass();
        $pass->process($this->containerBuilderMock);
    }

    /**
     * Ensure that registerProvider on AccountManager is never called whereas
     * registerProvider on CloudManager is called one when no account
     * provider and one cloud provider is registered.
     */
    public function testWithOneCloudProvider()
    {
        $this->accountManagerDefinitionMock->expects($this->never())
            ->method("addMethodCall");
        $this->cloudManagerDefinitionMock->expects($this->once())
            ->method("addMethodCall")
            ->with($this->equalTo("registerProvider"));
        $returnValueMap = array(
            array("xabbuh_panda.account_provider", array()),
            array("xabbuh_panda.cloud_provider", array("id" => array()))
        );
        $this->containerBuilderMock->expects($this->any())
            ->method("findTaggedServiceIds")
            ->will($this->returnValueMap($returnValueMap));
        $pass = new ProviderCompilerPass();
        $pass->process($this->containerBuilderMock);
    }

    /**
     * Ensure that registerProvider on CloudManager is called twice whereas
     * registerProvider on AccountManager is called never when two cloud
     * providers and no account provider is registered.
     */
    public function testWithTwoCloudProviders()
    {
        $this->accountManagerDefinitionMock->expects($this->never())
            ->method("addMethodCall");
        $this->cloudManagerDefinitionMock->expects($this->exactly(2))
            ->method("addMethodCall")
            ->with($this->equalTo("registerProvider"));
        $returnValueMap = array(
            array("xabbuh_panda.account_provider", array()),
            array(
                "xabbuh_panda.cloud_provider",
                array("id1" => array(), "id2" => array())
            )
        );
        $this->containerBuilderMock->expects($this->any())
            ->method("findTaggedServiceIds")
            ->will($this->returnValueMap($returnValueMap));
        $pass = new ProviderCompilerPass();
        $pass->process($this->containerBuilderMock);
    }

    /**
     * Ensure that registerProvider on AccountManager as well as
     * registerProvider on CloudManager is called once when one
     * account provider and one cloud provider is defined.
     */
    public function testWithOneAccountProviderAndOneCloudProvider()
    {
        $this->accountManagerDefinitionMock->expects($this->once())
            ->method("addMethodCall")
            ->with($this->equalTo("registerProvider"));
        $this->cloudManagerDefinitionMock->expects($this->once())
            ->method("addMethodCall")
            ->with($this->equalTo("registerProvider"));
        $returnValueMap = array(
            array("xabbuh_panda.account_provider", array("id1" => array())),
            array("xabbuh_panda.cloud_provider", array("id2" => array()))
        );
        $this->containerBuilderMock->expects($this->any())
            ->method("findTaggedServiceIds")
            ->will($this->returnValueMap($returnValueMap));
        $pass = new ProviderCompilerPass();
        $pass->process($this->containerBuilderMock);
    }
}
