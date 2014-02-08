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
 * Fetch data of a video.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class VideoInfoCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('panda:video:info');
        $this->setDescription('Fetch information for a video');
        $this->addArgument(
            'video-id',
            InputArgument::REQUIRED,
            'The id of the video being fetched'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cloud = $this->getCloud($input);
        $video = $cloud->getVideo($input->getArgument('video-id'));

        $output->writeln('id                 '.$video->getId());
        $output->writeln('file name          '.$video->getOriginalFilename());
        $output->writeln('width              '.$video->getWidth());
        $output->writeln('height             '.$video->getHeight());
        $output->writeln('audio bit rate     '.$video->getAudioBitrate());
        $output->writeln('video bit rate     '.$video->getVideoBitrate());
        $output->writeln('status             '.$video->getStatus());

        if ($video->getStatus() == 'fail') {
            $output->writeln('error message      '.$video->getErrorMessage());
        }

        $output->writeln('created at         '.$video->getCreatedAt());
        $output->writeln('updated at         '.$video->getUpdatedAt());
    }
}
