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

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Xabbuh\PandaBundle\Tests\DependencyInjection\Fixtures\Kernel;

class ContainerCompilationTest extends KernelTestCase
{
    public function testServicesCanBeBuilt()
    {
        $container = $this->bootKernel()->getContainer();

        foreach ($container->getParameter('xabbuh_panda.test_service_aliases') as $id => $type) {
            $this->assertInstanceOf($type, $container->get($id));
        }
    }

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}
