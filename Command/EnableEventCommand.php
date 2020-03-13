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
use Xabbuh\PandaClient\Model\NotificationEvent;
use Xabbuh\PandaClient\Model\Notifications;

/**
 * Command for enabling notification events.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @final since 1.5
 */
class EnableEventCommand extends CloudCommand
{
    protected static $defaultName = 'panda:notifications:enable';
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Enable a notification event');
        $this->addArgument(
            'event',
            InputArgument::REQUIRED,
            'The event being enabled'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $notificationEvent = new NotificationEvent($input->getArgument('event'), true);
        $notifications = new Notifications();
        $notifications->addNotificationEvent($notificationEvent);
        $this->getCloud($input)->setNotifications($notifications);
    }
}
