<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Implementation of a compiler pass which registers tagged services as
 * providers for clouds and accounts on the corresponding managers.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        // add account providers
        if ($container->hasDefinition("xabbuh_panda.account_manager")) {
            $accountManager = $container->getDefinition("xabbuh_panda.account_manager");
            $accountProviderServices = $container->findTaggedServiceIds("xabbuh_panda.account_provider");
            foreach ($accountProviderServices as $id => $attributes) {
                $accountManager->addMethodCall("registerProvider", array(new Reference($id)));
            }
        }

        // add cloud providers
        if ($container->hasDefinition("xabbuh_panda.cloud_manager")) {
            $cloudManager = $container->getDefinition("xabbuh_panda.cloud_manager");
            $cloudProviderServices = $container->findTaggedServiceIds("xabbuh_panda.cloud_provider");
            foreach ($cloudProviderServices as $id => $attributes) {
                $cloudManager->addMethodCall("registerProvider", array(new Reference($id)));
            }
        }
    }
}
