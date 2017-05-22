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

use Symfony\Component\Console\Helper\Table;
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
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $cloud = $this->getCloud($input);
        $video = $cloud->getVideo($input->getArgument('video-id'));

        $table = new Table($output);
        $table->addRows(array(
            array('id', $video->getId()),
            array('file name', $video->getOriginalFilename()),
            array('path', $video->getPath()),
            array('width', $video->getWidth()),
            array('height', $video->getHeight()),
            array('audio bit rate', $video->getAudioBitrate()),
            array('video bit rate', $video->getVideoBitrate()),
            array('status', $video->getStatus()),
            array('payload', $video->getPayload()),
        ));

        if ('fail' === $video->getStatus()) {
            $table->addRow(array('error message', $video->getErrorMessage()));
        }

        $table->addRows(array(
            array('created at', $video->getCreatedAt()),
            array('updated at', $video->getUpdatedAt()),
        ));
        $table->render($output);
    }
}
