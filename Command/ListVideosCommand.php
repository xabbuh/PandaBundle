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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to display a list of videos.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ListVideosCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('panda:video:list');
        $this->setDescription('List all videos of a cloud');
        $this->addOption(
            'page',
            null,
            InputOption::VALUE_REQUIRED,
            'Page to show',
            1
        );
        $this->addOption(
            'per-page',
            null,
            InputOption::VALUE_REQUIRED,
            'Number of videos per page',
            10
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->getCloud($input)->getVideosForPagination(
            $input->getOption('page'),
            $input->getOption('per-page')
        );

        $output->writeln(
            array(
                sprintf(
                    'Page %d of %d',
                    $result->page,
                    ceil($result->total / $result->per_page)
                ),
                'Total number of videos: '.$result->total,
            )
        );

        if (count($result->videos) > 0) {
            $output->writeln(sprintf(
                '% -32s    % -32s    %s',
                'video id',
                'encoding status',
                'file name'
            ));

            foreach ($result->videos as $video) {
                if ($video->getStatus() == 'fail') {
                    $status = sprintf(
                        'fail (%s)',
                        $video->getErrorMessage()
                    );
                } else if ($video->getStatus() == 'processing') {
                    $status = sprintf(
                        'proccessing (%d%%)',
                        $video->getEncodingProgress()
                    );
                } else {
                    $status = 'success';
                }

                $output->writeln(sprintf(
                    '% -32s    % -32s    %s',
                    $video->getId(),
                    $status,
                    $video->getOriginalFilename()
                ));
            }
        }
    }
}
