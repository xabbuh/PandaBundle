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
 * Command for displaying lists of encodings.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ListEncodingsCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName("panda:encoding:list");
        $this->setDescription("Display a (optionally filtered) list of encodings");
        $this->addOption(
            "status",
            null,
            InputOption::VALUE_REQUIRED,
            "Filter by status (one of 'success', 'fail' or 'processing')"
        );
        $this->addOption(
            "profile-id",
            null,
            InputOption::VALUE_REQUIRED,
            "Filter by a profile id"
        );
        $this->addOption(
            "profile-name",
            null,
            InputOption::VALUE_REQUIRED,
            "Filter by a profile name"
        );
        $this->addOption(
            "video-id",
            null,
            InputOption::VALUE_REQUIRED,
            "Filter by video id"
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = array();
        if ($input->getOption("status") != "") {
            $filter["status"] = $input->getOption("status");
        }
        if ($input->getOption("profile-id") != "") {
            $filter["profile_id"] = $input->getOption("profile-id");
        }
        if ($input->getOption("profile-name") != "") {
            $filter["profile_name"] = $input->getOption("profile-name");
        }
        if ($input->getOption("video-id") != "") {
            $filter["video_id"] = $input->getOption("video-id");
        }

        $encodings = $this->getCloud($input)->getEncodings($filter);
        if (count($encodings) > 0) {
            $output->writeln(sprintf(
                "% -32s    % -32s    % -32s    % -20s    %s",
                "encoding id",
                "video id",
                "profile id",
                "profile name",
                "encoding status"
            ));
            foreach ($encodings as $encoding) {
                if ($encoding->getStatus() == "fail") {
                    $status = sprintf(
                        "fail (%s)",
                        $encoding->getErrorMessage()
                    );
                } else if ($encoding->getStatus() == "processing") {
                    $status = sprintf(
                        "proccessing (%d%%)",
                        $encoding->getEncodingProgress()
                    );
                } else {
                    $status = "success";
                }
                $output->writeln(sprintf(
                    "% -32s    % -32s    % -32s    % -20s    %s",
                    $encoding->getId(),
                    $encoding->getVideoId(),
                    $encoding->getProfileId(),
                    $encoding->getProfileName(),
                    $status
                ));
            }
        }
    }
}
