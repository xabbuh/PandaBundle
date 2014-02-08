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
 * Command for upload video files into a Panda cloud.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class UploadVideoCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('panda:video:upload');
        $this->setDescription('Upload a video');
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
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting file upload...');
        $this->getCloud($input)->encodeVideoFile($input->getArgument('filename'));
        $output->writeln('<info>File successfully uploaded.</info>');
    }
}
