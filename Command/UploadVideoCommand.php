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
 * Command for upload video files into a Panda cloud.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @final
 */
class UploadVideoCommand extends CloudCommand
{
    protected static $defaultName = 'panda:video:upload';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Upload a video');
        $this->addOption(
            'profile',
            null,
            InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY,
            'A profile to be used to encode the video, use it several times to use more than one profile'
        );
        $this->addOption(
            'path-format',
            null,
            InputOption::VALUE_REQUIRED,
            'Custom path format'
        );
        $this->addOption(
            'payload',
            null,
            InputOption::VALUE_REQUIRED,
            'An arbitrary string stored with the video'
        );
        $this->addArgument(
            'filename',
            InputArgument::REQUIRED,
            'The file being uploaded'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $profiles = $input->getOption('profile');
        $pathFormat = $input->getOption('path-format');
        $payload = $input->getOption('payload');

        $output->writeln('Starting file upload...');
        $this->getCloud($input)->encodeVideoFile(
            $input->getArgument('filename'),
            $profiles,
            $pathFormat,
            $payload
        );
        $output->writeln('<info>File uploaded successfully.</info>');
    }
}
