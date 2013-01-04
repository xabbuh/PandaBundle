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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Fetch notification status information via command-line.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ShowNotificationsCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName("panda:notifications:show");
        $this->setDescription("Show the current notification configuration");

        parent::configure();
    }
    
    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $cloud = $this->getCloud($input);
        $notifications = $cloud->getNotifications();
        
        $output->writeln("url: " . $notifications->getUrl());
        
        $output->writeln("events");
        
        $output->write("  video-created:      ");
        if ($notifications->getNotificationEvent("video_created")->isActive()) {
            $output->writeln("enabled");
        } else {
            $output->writeln("disabled");
        }
        $output->write("  video-encoded:      ");
        if ($notifications->getNotificationEvent("video_encoded")->isActive()) {
            $output->writeln("enabled");
        } else {
            $output->writeln("disabled");
        }
        $output->write("  encoding-progress:  ");
        if ($notifications->getNotificationEvent("encoding_progress")->isActive()) {
            $output->writeln("enabled");
        } else {
            $output->writeln("disabled");
        }
        $output->write("  encoding-completed: ");
        if ($notifications->getNotificationEvent("encoding_completed")->isActive()) {
            $output->writeln("enabled");
        } else {
            $output->writeln("disabled");
        }
    }
}
