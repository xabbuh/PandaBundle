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
use Xabbuh\PandaClient\Exception\PandaException;

/**
 * Command to modify a profile.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ModifyProfileCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('panda:profile:modify');
        $this->setDescription('Modify a profile');
        $this->addArgument(
            'id',
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
            'video-bit rate',
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
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $profile = $this->getCloud($input)->getProfile($input->getArgument('id'));

            if ($input->hasOption('name')) {
                $profile->setName($input->getOption('name'));
            }

            if ($input->hasOption('title')) {
                $profile->setTitle($input->getOption('title'));
            }

            if ($input->hasOption('extname')) {
                $profile->setExtname($input->getOption('extname'));
            }

            if ($input->hasOption('width')) {
                $profile->setWidth($input->getOption('width'));
            }

            if ($input->hasOption('height')) {
                $profile->setHeight($input->getOption('height'));
            }

            if ($input->hasOption('audio-bitrate')) {
                $profile->setAudioBitrate($input->getOption('audio-bitrate'));
            }

            if ($input->hasOption('video-bitrate')) {
                $profile->setVideoBitrate($input->getOption('video-bitrate'));
            }

            if ($input->hasOption('aspect-mode')) {
                $profile->setAspectMode($input->getOption('aspect-mode'));
            }

            $this->getCloud($input)->setProfile($profile);

            $output->writeln(
                sprintf(
                    '<info>Successfully modified profile %s</info>',
                    $profile->getName()
                )
            );
        } catch(PandaException $e) {
            $output->writeln(
                '<error>An error occurred while trying to modify the profile: '
                .$e->getMessage().'</error>'
            );
        }
    }
}
