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
use Xabbuh\PandaClient\Exception\PandaException;

/**
 * Command to delete a profile.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class DeleteProfileCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('panda:profile:delete');
        $this->setDescription('Delete a profile');
        $this->addArgument(
            'id',
            InputArgument::REQUIRED,
            'Id of the profile'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $profile = $this->getCloud($input)->getProfile($input->getArgument('id'));
            $this->getCloud($input)->deleteProfile($profile);
            $output->writeln(
                sprintf(
                    'Successfully deleted profile %s',
                    $profile->getName()
                )
            );
        } catch(PandaException $e) {
            $output->writeln(
                'An error occurred while trying to delete the profile: '.$e->getMessage()
            );
        }
    }
}
