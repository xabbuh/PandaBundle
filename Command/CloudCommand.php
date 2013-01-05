<?php

namespace Xabbuh\PandaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Base class of all commands which act on panda clouds.
 *
 * The cloud name can be specified on the command-line. If no cloud is
 * given the configured default cloud is used.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class CloudCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->addOption(
            "cloud",
            "-c",
            InputOption::VALUE_REQUIRED,
            "Cloud on which the command is executed."
        );
    }

    /**
     * Get the cloud to work on.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return \Xabbuh\PandaBundle\Cloud\Cloud
     */
    protected function getCloud(InputInterface $input)
    {
        $key = $input->getOption("cloud");
        $cm = $this->getContainer()->get("xabbuh_panda.cloud_manager");

        if ($key == null) {
            $cloud = $cm->getDefaultCloud();
        } else {
            $cloud = $cm->getCloud($key);
        }

        return $cloud;
    }
}
