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
            'Xabbuh\PandaClient\Transformer\TransformerRegistry',
            $this->container->getParameter('xabbuh_panda.transformer.registry.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\CloudTransformer',
            $this->container->getParameter('xabbuh_panda.transformer.cloud.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\EncodingTransformer',
            $this->container->getParameter('xabbuh_panda.transformer.encoding.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\NotificationsTransformer',
            $this->container->getParameter('xabbuh_panda.transformer.notifications.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\ProfileTransformer',
            $this->container->getParameter('xabbuh_panda.transformer.profile.class')
        );
        $this->assertEquals(
            'Xabbuh\PandaClient\Transformer\VideoTransformer',
            $this->container->getParameter('xabbuh_panda.transformer.video.class')
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

    public function testTransformerServices()
    {
        $this->extension->load(array(), $this->container);

        $cloudTransformerId = 'xabbuh_panda.transformer.cloud';
        $cloudTransformer = $this->container->findDefinition($cloudTransformerId);
        $encodingTransformerId = 'xabbuh_panda.transformer.encoding';
        $encodingTransformer = $this->container->findDefinition($encodingTransformerId);
        $notificationsTransformerId = 'xabbuh_panda.transformer.notifications';
        $profileTransformerId = 'xabbuh_panda.transformer.profile';
        $profileTransformer = $this->container->findDefinition($profileTransformerId);
        $videoTransformerId = 'xabbuh_panda.transformer.video';
        $videoTransformer = $this->container->findDefinition($videoTransformerId);

        // ensure that serializers are passed to the transformers
        $this->assertEquals(
            'xabbuh_panda.serializer.cloud',
            $this->getMethodArgument($cloudTransformer->getMethodCalls(), 0, 0)
        );
        $this->assertEquals(
            'xabbuh_panda.serializer.encoding',
            $this->getMethodArgument($encodingTransformer->getMethodCalls(), 0, 0)
        );
        $this->assertEquals(
            'xabbuh_panda.serializer.profile',
            $this->getMethodArgument($profileTransformer->getMethodCalls(), 0, 0)
        );
        $this->assertEquals(
            'xabbuh_panda.serializer.video',
            $this->getMethodArgument($videoTransformer->getMethodCalls(), 0, 0)
        );

        $transformerRegistry = $this->container->findDefinition('xabbuh_panda.transformer');

        // ensure that encoders are passed to the transformer registry
        $this->assertEquals(
            'setCloudTransformer',
            $this->getMethodName($transformerRegistry->getMethodCalls(), 0)
        );
        $this->assertEquals(
            $cloudTransformerId,
            $this->getMethodArgument($transformerRegistry->getMethodCalls(), 0, 0)
        );
        $this->assertEquals(
            'setEncodingTransformer',
            $this->getMethodName($transformerRegistry->getMethodCalls(), 1)
        );
        $this->assertEquals(
            $encodingTransformerId,
            $this->getMethodArgument($transformerRegistry->getMethodCalls(), 1, 0)
        );
        $this->assertEquals(
            'setNotificationsTransformer',
            $this->getMethodName($transformerRegistry->getMethodCalls(), 2)
        );
        $this->assertEquals(
            $notificationsTransformerId,
            $this->getMethodArgument($transformerRegistry->getMethodCalls(), 2, 0)
        );
        $this->assertEquals(
            'setProfileTransformer',
            $this->getMethodName($transformerRegistry->getMethodCalls(), 3)
        );
        $this->assertEquals(
            $profileTransformerId,
            $this->getMethodArgument($transformerRegistry->getMethodCalls(), 3, 0)
        );
        $this->assertEquals(
            'setVideoTransformer',
            $this->getMethodName($transformerRegistry->getMethodCalls(), 4)
        );
        $this->assertEquals(
            $videoTransformerId,
            $this->getMethodArgument($transformerRegistry->getMethodCalls(), 4, 0)
        );
    }

    private function getMethodName($methodCalls, $number)
    {
        return $methodCalls[$number][0];
    }

    private function getMethodArgument($methodCalls, $number, $argument)
    {
        return $methodCalls[$number][1][$argument];
    }
}
