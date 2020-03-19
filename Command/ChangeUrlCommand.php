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
use Xabbuh\PandaClient\Model\Notifications;

/**
 * Command for modifying the endpoint of notification requests.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class ChangeUrlCommand extends CloudCommand
{
    protected static $defaultName = 'panda:notifications:change-url';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Change the endpoint for notification requests');
        $this->addArgument('url', InputArgument::REQUIRED, 'The new url', null);

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $notifications = new Notifications();
        $notifications->setUrl($input->getArgument('url'));
        $cloud = $this->getCloud($input);
        $cloud->setNotifications($notifications);
    }
}
