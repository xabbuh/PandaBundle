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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to display cloud details.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CloudInfoCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName("panda:cloud:info");
        $this->setDescription("Display details of a cloud");
        $this->addArgument(
            "cloud-id",
            InputArgument::REQUIRED,
            "The id of the cloud being fetched"
        );
        $this->addOption(
            "account",
            null,
            InputOption::VALUE_REQUIRED,
            "The account to use to authenticate to the panda service"
        );
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cloudFactory = $this->getContainer()->get("xabbuh_panda.cloud_factory");
        $cloud = $cloudFactory->get(
            $input->getArgument("cloud-id"),
            $input->getOption("account")
        );

        $cloudModel = $cloud->getCloudData();

        $output->writeln("id                   " . $cloudModel->getId());
        $output->writeln("name                 " . $cloudModel->getName());
        $output->writeln("s3 videos bucket     " . $cloudModel->getS3VideosBucket());
        $output->writeln("s3 private access    " . ($cloudModel->isS3AccessPrivate() ? "yes" : "no"));
        $output->writeln("url                  " . $cloudModel->getUrl());
        $output->writeln("created at           " . $cloudModel->getCreatedAt());
        $output->writeln("updated at           " . $cloudModel->getUpdatedAt());
    }
}
