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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xabbuh\PandaClient\Api\CloudManagerInterface;
use Xabbuh\PandaClient\Exception\PandaException;

/**
 * Base class of all commands which act on panda clouds.
 *
 * The cloud name can be specified on the command-line. If no cloud is
 * given the configured default cloud is used.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class CloudCommand extends Command
{
    private $cloudManager;

    public function __construct(CloudManagerInterface $cloudManager)
    {
        parent::__construct();

        $this->cloudManager = $cloudManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName(static::$defaultName); // BC with Symfony Console 3.3 and older not handling the property automatically
        $this->addOption(
            'cloud',
            '-c',
            InputOption::VALUE_REQUIRED,
            'Cloud on which the command is executed.'
        );
    }

    /**
     * @return \Xabbuh\PandaClient\Api\CloudManager
     */
    protected function getCloudManager()
    {
        return $this->cloudManager;
    }

    /**
     * Get the cloud to work on.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \Xabbuh\PandaClient\Api\CloudInterface
     */
    protected function getCloud(InputInterface $input)
    {
        if (null === $input->getOption('cloud')) {
            return $this->getCloudManager()->getDefaultCloud();
        }

        return $this->getCloudManager()->getCloud($input->getOption('cloud'));
    }

    /**
     * Executes the actual command (to be implemented by subclasses, will be called automatically).
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    abstract protected function doExecuteCommand(InputInterface $input, OutputInterface $output);

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->doExecuteCommand($input, $output);
        } catch (PandaException $e) {
            $output->writeln(sprintf('<error>An error occurred: %s</error>', $e->getMessage()));
        }
    }
}
