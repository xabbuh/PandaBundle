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

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Xabbuh\PandaBundle\XabbuhPandaBundle;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new XabbuhPandaBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->setParameter('kernel.secret', __FILE__);

            $routerConfig = [
                'resource' => __DIR__ . '/../../../Resources/config/routing.xml',
            ];

            if (class_exists(KernelBrowser::class)) {
                $routerConfig['utf8'] = true;
            }

            $container->loadFromExtension('framework', [
                'router' => $routerConfig,
            ]);
        });
    }

    public function getCacheDir(): string
    {
        return sprintf('%s/XabbuhPandaBundle/%d/cache', sys_get_temp_dir(), self::VERSION_ID);
    }

    public function getLogDir(): string
    {
        return sprintf('%s/XabbuhPandaBundle/%d/log', sys_get_temp_dir(), self::VERSION_ID);
    }

    public function process(ContainerBuilder $container): void
    {
        $testAliases = [];
        $deprecatedTestAliases = [];

        foreach ($container->getDefinitions() as $id => $definition) {
            if (0 === strpos($id, 'xabbuh_panda.') && !$definition->isAbstract() && !$definition->isDeprecated()) {
                $container->setAlias('test.'.$id, new Alias($id, true));
                $testAliases['test.'.$id] = $definition->getClass();
            } elseif (0 === strpos($id, 'xabbuh_panda.') && !$definition->isAbstract()) {
                $container->setAlias('test.'.$id, new Alias($id, true));
                $deprecatedTestAliases['test.'.$id] = $definition->getClass();
            }
        }

        $container->setParameter('xabbuh_panda.test_service_aliases', $testAliases);
        $container->setParameter('xabbuh_panda.deprecated_test_service_aliases', $deprecatedTestAliases);
    }
}
