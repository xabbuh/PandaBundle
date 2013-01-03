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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xabbuh\PandaBundle\Model\NotificationEvent;
use Xabbuh\PandaBundle\Model\Notifications;

/**
 * Command for disabling notification events.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class DisableEventCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc} 
     */
    public function configure()
    {
        $this->setName("panda:notifications:disable");
        $this->setDescription("Disable a notification event");
        $this->addArgument(
            "event",
            InputArgument::REQUIRED,
            "The event being disabled"
        );
    }
    
    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $event = strtr($input->getArgument("event"), "-", "_");
        $notificationEvent = new NotificationEvent($event, false);
        $notifications = new Notifications();
        $notifications->addNotificationEvent($notificationEvent);
        $cloud = $this->getContainer()->get("xabbuh_panda.cloud_manager")
            ->getDefaultCloud();
        $cloud->setNotifications($notifications);
    }
}
