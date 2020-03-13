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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to create a profile based on a preset.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @final
 */
class CreateProfileCommand extends CloudCommand
{
    protected static $defaultName = 'panda:profile:create';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Create a profile based on a given preset');
        $this->addArgument(
        'preset',
            InputArgument::REQUIRED,
            'Name of the preset to use'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $cloud = $this->getCloud($input);
        $preset = $input->getArgument('preset');
        $profile = $cloud->addProfileFromPreset($preset);
        $output->writeln(
            sprintf(
                '<info>Successfully created profile %s with id %s</info>',
                $profile->getName(),
                $profile->getId()
            )
        );
    }
}
