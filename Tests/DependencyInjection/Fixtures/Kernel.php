<?php

/*
 * This file is part of the XabbuhPandaBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\PandaBundle\Tests\DependencyInjection\Fixtures;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Xabbuh\PandaBundle\XabbuhPandaBundle;

class Kernel extends BaseKernel
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
            $container->addCompilerPass(new PublicTestAliasPass());
            $container->setParameter('kernel.secret', __FILE__);

            $container->loadFromExtension('framework', [
                'router' => [
                    'resource' => __DIR__ . '/../../../Resources/config/routing.xml',
                ],
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
}
