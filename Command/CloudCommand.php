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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Xabbuh\PandaClient\Api\CloudManagerInterface;
use Xabbuh\PandaClient\Exception\PandaException;

if (class_exists(ContainerAwareCommand::class)) {
    /**
     * Base class of all commands which act on panda clouds.
     *
     * The cloud name can be specified on the command-line. If no cloud is
     * given the configured default cloud is used.
     *
     * @author Christian Flothmann <christian.flothmann@xabbuh.de>
     *
     * @internal since 1.5
     */
    abstract class CloudCommand extends ContainerAwareCommand
    {
        use CloudCommandTrait;

        private $cloudManager;
        private $container;

        public function __construct(CloudManagerInterface $cloudManager = null)
        {
            if (null === $cloudManager) {
                @trigger_error(sprintf('Not injecting a %s instance into the constructor of the %s class is deprecated since PandaBundle 1.5.', CloudManagerInterface::class, static::class), E_USER_DEPRECATED);
            }

            parent::__construct();

            $this->cloudManager = $cloudManager;
        }

        public function setContainer(ContainerInterface $container = null)
        {
            @trigger_error(sprintf('The %s() method is deprecated since PandaBundle 1.5.', __METHOD__), E_USER_DEPRECATED);

            parent::setContainer($container);
        }

        public function getContainer()
        {
            @trigger_error(sprintf('The %s() method is deprecated since PandaBundle 1.5.', __METHOD__), E_USER_DEPRECATED);

            return parent::getContainer();
        }
    }
} else {
    /**
     * Base class of all commands which act on panda clouds.
     *
     * The cloud name can be specified on the command-line. If no cloud is
     * given the configured default cloud is used.
     *
     * @author Christian Flothmann <christian.flothmann@xabbuh.de>
     *
     * @internal since 1.5
     */
    abstract class CloudCommand extends Command
    {
        use CloudCommandTrait;

        private $cloudManager;
        private $container;

        public function __construct(CloudManagerInterface $cloudManager = null)
        {
            if (null === $cloudManager) {
                @trigger_error(sprintf('Not injecting a %s instance into the constructor of the %s class is deprecated since PandaBundle 1.5.', CloudManagerInterface::class, static::class), E_USER_DEPRECATED);
            }

            parent::__construct();

            $this->cloudManager = $cloudManager;
        }

        public function setContainer(ContainerInterface $container = null)
        {
            @trigger_error(sprintf('The %s() method is deprecated since PandaBundle 1.5.', __METHOD__), E_USER_DEPRECATED);

            $this->container = $container;
        }

        public function getContainer()
        {
            @trigger_error(sprintf('The %s() method is deprecated since PandaBundle 1.5.', __METHOD__), E_USER_DEPRECATED);

            return $this->container;
        }
    }
}

/**
 * @internal
 */
trait CloudCommandTrait
{
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
        if (null !== $this->cloudManager) {
            return $this->cloudManager;
        }

        return $this->getContainer()->get('xabbuh_panda.cloud_manager');
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

            return 0;
        } catch (PandaException $e) {
            $output->writeln(sprintf('<error>An error occurred: %s</error>', $e->getMessage()));

            return 1;
        }
    }
}
