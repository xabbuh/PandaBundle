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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Fetch notification status information via command-line.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @final since 1.5
 */
class ShowNotificationsCommand extends CloudCommand
{
    protected static $defaultName = 'panda:notifications:show';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Show the current notification configuration');

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $notifications = $this->getCloud($input)->getNotifications();
        $table = new Table($output);
        $table->addRow(array('url', $notifications->getUrl()));

        if ($notifications->getNotificationEvent('video_created')->isActive()) {
            $table->addRow(array('video-created', '<info>enabled</info>'));
        } else {
            $table->addRow(array('video-created', 'disabled'));
        }

        if ($notifications->getNotificationEvent('video_encoded')->isActive()) {
            $table->addRow(array('video-encoded', '<info>enabled</info>'));
        } else {
            $table->addRow(array('video-encoded', 'disabled'));
        }

        if ($notifications->getNotificationEvent('encoding_progress')->isActive()) {
            $table->addRow(array('encoding-progress', '<info>enabled</info>'));
        } else {
            $table->addRow(array('encoding-progress', 'disabled'));
        }

        if ($notifications->getNotificationEvent('encoding_completed')->isActive()) {
            $table->addRow(array('encoding-completed', '<info>enabled</info>'));
        } else {
            $table->addRow(array('encoding-completed', 'disabled'));
        }

        $table->render($output);
    }
}
