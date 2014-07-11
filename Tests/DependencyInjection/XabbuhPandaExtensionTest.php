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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Xabbuh\PandaBundle\DependencyInjection\XabbuhPandaExtension;

/**
 * Tests the XabbuhPandaExtension class.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XabbuhPandaExtensionTest extends AbstractExtensionTestCase
{
    /**
     * Tests the extension without custom config options.
     */
    public function testDefaultConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('xabbuh_panda.account.default', 'default');
        $this->assertContainerBuilderHasParameter('xabbuh_panda.cloud.default', 'default');
        $this->assertContainerBuilderHasParameter(
            'xabbuh_panda.video_uploader.multiple_files',
            false
        );
        $this->assertContainerBuilderHasParameter(
            'xabbuh_panda.video_uploader.cancel_button',
            true
        );
        $this->assertContainerBuilderHasParameter(
            'xabbuh_panda.video_uploader.progress_bar',
            true
        );

        $this->ensureThatDefinitionsAreRegistered(array(
            'xabbuh_panda.account_manager' => 'Xabbuh\PandaClient\Api\AccountManager',
            'xabbuh_panda.cloud_manager' => 'Xabbuh\PandaClient\Api\CloudManager',
            'xabbuh_panda.cloud_factory' => 'Xabbuh\PandaBundle\Cloud\CloudFactory',
            'xabbuh_panda.controller' => 'Xabbuh\PandaBundle\Controller\Controller',
            'xabbuh_panda.transformer' => 'Xabbuh\PandaClient\Transformer\TransformerRegistry',
            'xabbuh_panda.transformer.cloud' => 'Xabbuh\PandaClient\Transformer\CloudTransformer',
            'xabbuh_panda.transformer.encoding' => 'Xabbuh\PandaClient\Transformer\EncodingTransformer',
            'xabbuh_panda.transformer.notifications' => 'Xabbuh\PandaClient\Transformer\NotificationsTransformer',
            'xabbuh_panda.transformer.profile' => 'Xabbuh\PandaClient\Transformer\ProfileTransformer',
            'xabbuh_panda.transformer.video' => 'Xabbuh\PandaClient\Transformer\VideoTransformer',
            'xabbuh_panda.video_uploader_extension' => 'Xabbuh\PandaBundle\Form\Extension\VideoUploaderExtension',
        ));
        $this->ensureThatSerializersArePassedToTransformers();
        $this->ensureThatTransformersArePassedToTheRegistry();
    }

    /**
     * Tests that the names of the default Account and the default Cloud can
     * be configured.
     */
    public function testModifiedDefaultNames()
    {
        $this->load(array('default_account' => 'foo', 'default_cloud' => 'bar'));

        $this->assertContainerBuilderHasParameter('xabbuh_panda.account.default', 'foo');
        $this->assertContainerBuilderHasParameter('xabbuh_panda.cloud.default', 'bar');
    }

    /**
     * Tests that an account key with the name of the default account is added
     * if no account key was given for a cloud definition.
     */
    public function testCloudWithoutAccountKey()
    {
        $this->load(array(
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
        ));
        $withAccountCloud = $this->container->findDefinition('xabbuh_panda.with_account_cloud');
        $withoutAccountCloud = $this->container->findDefinition('xabbuh_panda.without_account_cloud');

        $this->ensureThatDefinitionsAreRegistered(array(
            'xabbuh_panda.with_account_cloud' => 'Xabbuh\PandaClient\Api\Cloud',
            'xabbuh_panda.without_account_cloud' => 'Xabbuh\PandaClient\Api\Cloud',
        ));

        $this->assertContainerBuilderHasParameter('xabbuh_panda.account.default', 'default');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'xabbuh_panda.with_account_cloud',
            1,
            'bar'
        );
        $this->assertEquals('xabbuh_panda.cloud_factory', $withAccountCloud->getFactoryService());
        $this->assertEquals('get', $withAccountCloud->getFactoryMethod());
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'xabbuh_panda.without_account_cloud',
            1,
            null
        );
        $this->assertEquals('xabbuh_panda.cloud_factory', $withoutAccountCloud->getFactoryService());
        $this->assertEquals('get', $withoutAccountCloud->getFactoryMethod());
    }

    protected function getContainerExtensions()
    {
        return array(new XabbuhPandaExtension());
    }

    private function ensureThatDefinitionsAreRegistered(array $definitions)
    {
        foreach ($definitions as $id => $className) {
            $this->assertContainerBuilderHasService($id, $className);
        }
    }

    private function ensureThatSerializersArePassedToTransformers()
    {
        $transformerTypes = array('cloud', 'encoding', 'profile', 'video');

        foreach ($transformerTypes as $type) {
            $definition = $this->container->findDefinition('xabbuh_panda.transformer.'.$type);
            $methodCalls = $definition->getMethodCalls();
            $this->validateMethodCallWithOneArgument(
                $methodCalls[0],
                'setSerializer',
                'xabbuh_panda.serializer.'.$type
            );
        }
    }

    private function ensureThatTransformersArePassedToTheRegistry()
    {
        $transformerRegistry = $this->container->findDefinition('xabbuh_panda.transformer');
        $methodCalls = $transformerRegistry->getMethodCalls();
        $transformerTypes = array('Cloud', 'Encoding', 'Notifications', 'Profile', 'Video');

        for ($i = 0; $i < count($transformerTypes); $i++) {
            $this->validateMethodCallWithOneArgument(
                $methodCalls[$i],
                'set'.$transformerTypes[$i].'Transformer',
                'xabbuh_panda.transformer.'.strtolower($transformerTypes[$i])
            );
        }
    }

    private function validateMethodCallWithOneArgument(array $methodCall, $expectedMethodName, $expectedArgument)
    {
        $actualMethodName = $methodCall[0];
        $actualArgument = $methodCall[1][0];

        $this->assertEquals($expectedMethodName, $actualMethodName);
        $this->assertEquals($expectedArgument, $actualArgument);
    }
}
