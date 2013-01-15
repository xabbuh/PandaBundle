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
 * Show a profile's details.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ProfileInfoCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName("panda:profile:info");
        $this->setDescription("Fetch information for a profile");
        $this->addArgument("profile-id", InputArgument::REQUIRED, "The id of the profile being fetched");

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cloud = $this->getCloud($input);
        $profile = $cloud->getProfile($input->getArgument("profile-id"));
        $output->writeln("id               " . $profile->getId());
        $output->writeln("title            " . $profile->getTitle());
        $output->writeln("name             " . $profile->getName());
        $output->writeln("extname          " . $profile->getExtname());
        $output->writeln("width            " . $profile->getWidth());
        $output->writeln("height           " . $profile->getHeight());
        $output->writeln("audio bitrate    " . $profile->getAudioBitrate());
        $output->writeln("video bitrate    " . $profile->getVideoBitrate());
        $output->writeln("aspect mode      " . $profile->getAspectMode());
        $output->writeln("created at       " . $profile->getCreatedAt());
        $output->writeln("updated at       " . $profile->getUpdatedAt());
    }
}
