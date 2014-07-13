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
use Xabbuh\PandaClient\Exception\PandaException;
use Xabbuh\PandaClient\Model\Video;

/**
 * Delete a video on the command-line.
 *
 * @author Christian Flothmann <chrsitian.flothmann@xabbuh.de>
 */
class DeleteVideoCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected  function configure()
    {
        $this->setName('panda:video:delete');
        $this->setDescription('Delete a video');
        $this->addArgument(
            'video-id',
            InputArgument::REQUIRED,
            'Id of the video'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $videoId = $input->getArgument('video-id');
        $video = new Video();
        $video->setId($videoId);
        $this->getCloud($input)->deleteVideo($video);
        $output->writeln('<info>Successfully deleted video with id '.$videoId.'</info>');
    }
}
