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
 * List all configured profiles for a cloud.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ListProfilesCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('panda:profile:list');
        $this->setDescription('List all profiles of a cloud');

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $profiles = $this->getCloud($input)->getProfiles();

        if (count($profiles) > 0) {
            $table = $this->getTableHelper();
            $table->setHeaders(array('profile id', 'profile name'));

            foreach ($this->getCloud($input)->getProfiles() as $profile) {
                $table->addRow(array(
                    $profile->getId(),
                    $profile->getTitle(),
                ));
            }

            $table->render($output);
        }
    }
}
