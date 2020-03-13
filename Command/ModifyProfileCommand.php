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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to modify a profile.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @final since 1.5
 */
class ModifyProfileCommand extends CloudCommand
{
    protected static $defaultName = 'panda:profile:modify';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Modify a profile');
        $this->addArgument(
            'profile-id',
            InputArgument::REQUIRED,
            'Id of the profile'
        );
        $this->addOption(
            'name',
            null,
            InputOption::VALUE_REQUIRED,
            'Machine-readable unique name'
        );
        $this->addOption(
            'title',
            null,
            InputOption::VALUE_REQUIRED,
            'Human-readable name'
        );
        $this->addOption(
            'extname',
            null,
            InputOption::VALUE_REQUIRED,
            'File extension'
        );
        $this->addOption(
            'width',
            null,
            InputOption::VALUE_REQUIRED,
            'Width in pixels'
        );
        $this->addOption(
            'height',
            null,
            InputOption::VALUE_REQUIRED,
            'Height in Pixels'
        );
        $this->addOption(
            'audio-bitrate',
            null,
            InputOption::VALUE_REQUIRED,
            'Audio bit rate'
        );
        $this->addOption(
            'video-bitrate',
            null,
            InputOption::VALUE_REQUIRED,
            'Video bit rate'
        );
        $this->addOption(
            'aspect-mode',
            null,
            InputOption::VALUE_REQUIRED,
            'Aspect mode'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $profile = $this->getCloud($input)
            ->getProfile($input->getArgument('profile-id'));

        if (null !== $input->getOption('name')) {
            $profile->setName($input->getOption('name'));
        }

        if (null !== $input->getOption('title')) {
            $profile->setTitle($input->getOption('title'));
        }

        if (null !== $input->getOption('extname')) {
            $profile->setExtname($input->getOption('extname'));
        }

        if (null !== $input->getOption('width')) {
            $profile->setWidth($input->getOption('width'));
        }

        if (null !== $input->getOption('height')) {
            $profile->setHeight($input->getOption('height'));
        }

        if (null !== $input->getOption('audio-bitrate')) {
            $profile->setAudioBitrate($input->getOption('audio-bitrate'));
        }

        if (null !== $input->getOption('video-bitrate')) {
            $profile->setVideoBitrate($input->getOption('video-bitrate'));
        }

        if (null !== $input->getOption('aspect-mode')) {
            $profile->setAspectMode($input->getOption('aspect-mode'));
        }

        $this->getCloud($input)->setProfile($profile);

        $output->writeln(
            sprintf(
                '<info>Successfully modified profile %s</info>',
                $profile->getName()
            )
        );
    }
}
