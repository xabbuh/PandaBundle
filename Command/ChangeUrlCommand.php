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
use Xabbuh\PandaBundle\Model\Notifications;

/**
 * Command for modifying the endpoint of notification requests.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ChangeUrlCommand extends CloudCommand
{
    /**
     * {@inheritDoc} 
     */
    public function configure()
    {
        $this->setName("panda:notifications:change-url");
        $this->setDescription("Change the endpoint for notification requests");
        $this->addArgument("url", InputArgument::REQUIRED, "The new url", null);

        parent::configure();
    }
    
    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $notifications = new Notifications();
        $notifications->setUrl($input->getArgument("url"));
        $cloud = $this->getCloud($input);
        $cloud->setNotifications($notifications);
    }
}
