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
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cloud = $this->getCloud($input)->getCloud();

        $output->writeln('id                   '.$cloud->getId());
        $output->writeln('name                 '.$cloud->getName());
        $output->writeln('s3 videos bucket     '.$cloud->getS3VideosBucket());
        $output->writeln('s3 private access    '.($cloud->isS3AccessPrivate() ? 'yes' : 'no'));
        $output->writeln('url                  '.$cloud->getUrl());
        $output->writeln('created at           '.$cloud->getCreatedAt());
        $output->writeln('updated at           '.$cloud->getUpdatedAt());
    }
}
