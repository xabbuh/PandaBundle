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
 * Fetch an encoding's data.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class EncodingInfoCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('panda:encoding:info');
        $this->setDescription('Fetch information for an encoding');
        $this->addArgument(
            'encoding-id',
            InputArgument::REQUIRED,
            'The id of the encoding being fetched'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cloud = $this->getCloud($input);
        $encoding = $cloud->getEncoding($input->getArgument('encoding-id'));
        $table = $this->getTableHelper();
        $table->addRows(array(
            array('id', $encoding->getId()),
            array('video id', $encoding->getVideoId()),
            array('file extension', $encoding->getExtname()),
            array('profile id', $encoding->getProfileId()),
            array('profile name', $encoding->getProfileName()),
            array('path', $encoding->getPath()),
            array('width', $encoding->getWidth()),
            array('height', $encoding->getHeight()),
            array('audio bit rate', $encoding->getAudioBitrate()),
            array('video bit rate', $encoding->getVideoBitrate()),
            array('status', $encoding->getStatus()),
        ));

        if ('fail' === $encoding->getStatus()) {
            $table->addRow(array('error message', $encoding->getErrorMessage()));
        }

        $table->addRows(array(
            array('encoding progress', $encoding->getEncodingProgress()),
            array('encoding started', $encoding->getStartedEncodingAt()),
            array('encoding time', $encoding->getEncodingTime()),
            array('created at', $encoding->getCreatedAt()),
            array('updated at', $encoding->getUpdatedAt()),
        ));
        $table->render($output);
    }
}
