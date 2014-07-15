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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Xabbuh\PandaBundle\DependencyInjection\Compiler\ProviderPass;

/**
 * Test the compiler pass that registers services as account or cloud
 * providers if they are tagged as such.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ProviderPassTest extends AbstractCompilerPassTestCase
{
    public function testWithoutProviders()
    {
        $this->validateContainerWithTaggedProviders(0, 0);
    }

    public function testWithOneAccountProvider()
    {
        $this->validateContainerWithTaggedProviders(1, 0);
    }

    public function testWithTwoAccountProviders()
    {
        $this->validateContainerWithTaggedProviders(2, 0);
    }

    public function testWithOneCloudProvider()
    {
        $this->validateContainerWithTaggedProviders(0, 1);
    }

    public function testWithTwoCloudProviders()
    {
        $this->validateContainerWithTaggedProviders(0, 2);
    }

    public function testWithOneAccountProviderAndOneCloudProvider()
    {
        $this->validateContainerWithTaggedProviders(1, 1);
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ProviderPass());
    }

    private function validateContainerWithTaggedProviders($accountProvidersCount, $cloudProvidersCount)
    {
        $this->container->register(
            'xabbuh_panda.account_manager',
            'Xabbuh\PandaClient\Api\AccountManager'
        );
        $this->container->register(
            'xabbuh_panda.cloud_manager',
            'Xabbuh\PandaClient\Api\CloudManager'
        );

        for ($i = 0; $i < $accountProvidersCount; $i++) {
            $definition = new Definition('Foo');
            $definition->addTag('xabbuh_panda.account_provider');
            $this->container->setDefinition(md5(uniqid()), $definition);
        }

        for ($i = 0; $i < $cloudProvidersCount; $i++) {
            $definition = new Definition('Foo');
            $definition->addTag('xabbuh_panda.cloud_provider');
            $this->container->setDefinition(md5(uniqid()), $definition);
        }

        $this->compile();

        $this->assertEquals(
            $accountProvidersCount,
            count($this->container->getDefinition('xabbuh_panda.account_manager')->getMethodCalls())
        );
        $this->assertEquals(
            $cloudProvidersCount,
            count($this->container->getDefinition('xabbuh_panda.cloud_manager')->getMethodCalls())
        );
    }
}
