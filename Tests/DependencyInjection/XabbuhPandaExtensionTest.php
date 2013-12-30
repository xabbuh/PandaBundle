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
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var XabbuhPandaExtension
     */
    private $extension;
    
    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new XabbuhPandaExtension();
    }
    
    /**
     * Tests the extension without custom config options.
     */
    public function testDefaultConfig()
    {
        $this->extension->load(array(), $this->container);

        // and check that all parameters do exist and match the default configuration values
        $this->assertEquals(
            'default',
            $this->container->getParameter('xabbuh_panda.account.default')
        );
        $this->assertEquals(
            'default',
            $this->container->getParameter('xabbuh_panda.cloud.default')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Api\AccountManager',
            $this->container->getParameter('xabbuh_panda.account.manager.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Api\CloudManager',
            $this->container->getParameter('xabbuh_panda.cloud.manager.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaBundle\Cloud\CloudFactory',
            $this->container->getParameter('xabbuh_panda.cloud.factory.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaBundle\Controller\Controller',
            $this->container->getParameter('xabbuh_panda.controller.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\TransformerFactory',
            $this->container->getParameter('xabbuh_panda.transformer.factory.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\CloudTransformer',
            $this->container->getParameter('xabbuh_panda.transformer.model.cloud.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\EncodingTransformer',
            $this->container->getParameter('xabbuh_panda.transformer.model.encoding.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\NotificationsTransformer',
            $this->container->getParameter('xabbuh_panda.transformer.model.notifications.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\ProfileTransformer',
            $this->container->getParameter('xabbuh_panda.transformer.model.profile.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\VideoTransformer',
            $this->container->getParameter('xabbuh_panda.transformer.model.video.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaBundle\Form\Extension\VideoUploaderExtension',
            $this->container->getParameter('xabbuh_panda.video_uploader_extension.class')
        );
        $this->assertFalse(
            $this->container->getParameter('xabbuh_panda.video_uploader.multiple_files')
        );
        $this->assertTrue(
            $this->container->getParameter('xabbuh_panda.video_uploader.cancel_button')
        );
        $this->assertTrue(
            $this->container->getParameter('xabbuh_panda.video_uploader.progress_bar')
        );
    }

    /**
     * Tests that the names of the default Account and the default Cloud can
     * be configured.
     */
    public function testModifiedDefaultNames()
    {
        $config = array('default_account' => 'foo', 'default_cloud' => 'bar');
        $this->extension->load(array($config), $this->container);
        $this->assertEquals(
            'foo',
            $this->container->getParameter('xabbuh_panda.account.default')
        );
        $this->assertEquals(
            'bar',
            $this->container->getParameter('xabbuh_panda.cloud.default')
        );
    }

    /**
     * Tests that an account key with the name of the default account is added
     * if no account key was given for a cloud definition.
     */
    public function testCloudWithoutAccountKey()
    {
        $config = array(
            'default_account' => 'default',
            'clouds' => array(
                'with_account' => array(
                    'id' => 'foo',
                    'account' => 'bar',
                ),
                'without_account' => array(
                    'id' => 'foobar',
                ),
            ),
        );
        $this->extension->load(array($config), $this->container);

        $this->assertEquals(
            'default',
            $this->container->getParameter('xabbuh_panda.account.default')
        );

        $fooCloud = $this->container->getDefinition('xabbuh_panda.with_account_cloud');
        $this->assertEquals('bar', $fooCloud->getArgument(1));
        $this->assertEquals('xabbuh_panda.cloud_factory', $fooCloud->getFactoryService());

        $foobarCloud = $this->container->getDefinition('xabbuh_panda.without_account_cloud');
        $this->assertNull($foobarCloud->getArgument(1));
        $this->assertEquals('xabbuh_panda.cloud_factory', $fooCloud->getFactoryService());
    }
}
