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
        $this->setName("panda:encoding:info");
        $this->setDescription("Fetch information for an encoding");
        $this->addArgument(
            "encoding-id",
            InputArgument::REQUIRED,
            "The id of the encoding being fetched"
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cloud = $this->getCloud($input);
        $encoding = $cloud->getEncoding($input->getArgument("encoding-id"));
        $output->writeln("id                 " . $encoding->getId());
        $output->writeln("video id           " . $encoding->getVideoId());
        $output->writeln("extname            " . $encoding->getExtname());
        $output->writeln("profile id         " . $encoding->getProfileId());
        $output->writeln("profile name       " . $encoding->getProfileName());
        $output->writeln("width              " . $encoding->getWidth());
        $output->writeln("height             " . $encoding->getHeight());
        $output->writeln("audio bitrate      " . $encoding->getAudioBitrate());
        $output->writeln("video bitrate      " . $encoding->getVideoBitrate());
        $output->writeln("status             " . $encoding->getStatus());
        if ($encoding->getStatus() == "fail") {
            $output->writeln("error message      " . $encoding->getErrorMessage());
        }
        $output->writeln("encoding progress  " . $encoding->getEncodingProgress() . "%");
        $output->writeln("encoding started   " . $encoding->getStartedEncodingAt());
        $output->writeln("encoding time      " . $encoding->getEncodingTime());
        $output->writeln("created at         " . $encoding->getCreatedAt());
        $output->writeln("updated at         " . $encoding->getUpdatedAt());
    }
}
