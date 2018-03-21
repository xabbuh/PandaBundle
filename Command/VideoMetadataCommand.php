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
 * Fetch and display metadata of a video.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class VideoMetadataCommand extends CloudCommand
{
    protected static $defaultName = 'panda:video:metadata';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Fetch metadata for a video');
        $this->addArgument(
            'video-id',
            InputArgument::REQUIRED,
            'The id of the video'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $cloud = $this->getCloud($input);
        $metadata = $cloud->getVideoMetadata($input->getArgument('video-id'));
        $table = new Table($output);

        foreach ($metadata as $key => $value) {
            $table->addRow(array(
                $key,
                is_array($value) ? implode(', ', $value) : $value,
            ));
        }

        $table->render($output);
    }
}
