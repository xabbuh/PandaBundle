<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Xabbuh\PandaBundle\DependencyInjection\Compiler\ProviderPass;

/**
 * XabbuhPandaBundle provides an interface for using the
 * {@link http://www.pandastream.com/ Panda Video Encoding Service} to encode
 * video files.
 * 
 * @author Christian Flothmann <christian.flothmann@xabbuh.de> 
 */
class XabbuhPandaBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProviderPass());
    }
}
