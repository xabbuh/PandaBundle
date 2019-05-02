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

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PublicTestAliasPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $testAliases = [];

        foreach ($container->getDefinitions() as $id => $definition) {
            if (0 === strpos($id, 'xabbuh_panda.') && !$definition->isAbstract()) {
                $container->setAlias('test.'.$id, new Alias($id, true));
                $testAliases['test.'.$id] = $definition->getClass();
            }
        }

        $container->setParameter('xabbuh_panda.test_service_aliases', $testAliases);
    }
}
