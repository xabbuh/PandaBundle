<?php

/*
 * This file is part of the XabbuhPandaBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\PandaBundle\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to display cloud details.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CloudInfoCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('panda:cloud:info');
        $this->setDescription('Display details of a cloud');

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $cloud = $this->getCloud($input)->getCloud();
        $table = new Table($output);
        $table->addRows(array(
            array('id', $cloud->getId()),
            array('name', $cloud->getName()),
            array('s3 videos bucket', $cloud->getS3VideosBucket()),
            array('s3 private access', $cloud->isS3AccessPrivate() ? 'yes' : 'no'),
            array('url', $cloud->getUrl()),
            array('created at', $cloud->getCreatedAt()),
            array('updated at', $cloud->getUpdatedAt()),
        ));
        $table->render($output);
    }
}
