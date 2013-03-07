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
use Xabbuh\PandaBundle\DependencyInjection\XabbuhPandaExtension;

/**
 * Tests the XabbuhPandaExtension class.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XabbuhPandaExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the extension without custom config options.
     */
    public function testDefaultConfig()
    {
        // load parsed config
        $container = new ContainerBuilder();
        $extension = new XabbuhPandaExtension();
        $extension->load(array(), $container);

        // and check that all parameters do exist and match the default configuration values
        $this->assertEquals(
            "default",
            $container->getParameter("xabbuh_panda.account.default")
        );
        $this->assertEquals(
            "default",
            $container->getParameter("xabbuh_panda.cloud.default")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Account\\AccountManager",
            $container->getParameter("xabbuh_panda.account.manager.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Account\\ConfigAccountProvider",
            $container->getParameter("xabbuh_panda.account.config_provider.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Cloud\\CloudManager",
            $container->getParameter("xabbuh_panda.cloud.manager.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Cloud\\CloudFactory",
            $container->getParameter("xabbuh_panda.cloud.factory.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Cloud\\ConfigCloudProvider",
            $container->getParameter("xabbuh_panda.cloud.config_provider.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Controller\\Controller",
            $container->getParameter("xabbuh_panda.controller.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Services\\TransformerFactory",
            $container->getParameter("xabbuh_panda.transformer.factory.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Transformers\\CloudTransformer",
            $container->getParameter("xabbuh_panda.transformer.model.cloud.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Transformers\\EncodingTransformer",
            $container->getParameter("xabbuh_panda.transformer.model.encoding.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Transformers\\NotificationsTransformer",
            $container->getParameter("xabbuh_panda.transformer.model.notifications.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Transformers\\ProfileTransformer",
            $container->getParameter("xabbuh_panda.transformer.model.profile.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Transformers\\VideoTransformer",
            $container->getParameter("xabbuh_panda.transformer.model.video.class")
        );
        $this->assertEquals(
            "Xabbuh\\PandaBundle\\Form\\Extension\\VideoUploaderExtension",
            $container->getParameter("xabbuh_panda.video_uploader_extension.class")
        );
        $this->assertFalse(
            $container->getParameter("xabbuh_panda.video_uploader.multiple_files")
        );
        $this->assertTrue(
            $container->getParameter("xabbuh_panda.video_uploader.cancel_button")
        );
        $this->assertTrue(
            $container->getParameter("xabbuh_panda.video_uploader.progress_bar")
        );
    }

    /**
     * Tests that the names of the default Account and the default Cloud can
     * be configured.
     */
    public function testModifiedDefaultNames()
    {
        $config = array("default_account" => "foo", "default_cloud" => "bar");
        $container = new ContainerBuilder();
        $extension = new XabbuhPandaExtension();
        $extension->load(array($config), $container);
        $this->assertEquals(
            "foo",
            $container->getParameter("xabbuh_panda.account.default")
        );
        $this->assertEquals(
            "bar",
            $container->getParameter("xabbuh_panda.cloud.default")
        );
    }

    /**
     * Tests that an account key with the name of the default account is added
     * if no account key was given for a cloud definition.
     */
    public function testCloudWithoutAccountKey()
    {
        $config = array("default_account" => "default",
            "clouds" => array(
                "with_account" => array(
                    "id" => "foo",
                    "account" => "bar"
                ),
                "without_account" => array(
                    "id" => "foobar"
                )
            )
        );
        $container = new ContainerBuilder();
        $extension = new XabbuhPandaExtension();
        $extension->load(array($config), $container);
        $providerDefinition = $container->getDefinition("xabbuh_panda.config_cloud_provider");
        $clouds = $providerDefinition->getArgument(1);
        $this->assertEquals(
            array(
                "id" => "foo",
                "account" => "bar"
            ),
            $clouds["with_account"]
        );
        $this->assertEquals(
            array(
                "id" => "foobar",
                "account" => "default"
            ),
            $clouds["without_account"]
        );
    }
}
