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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Xabbuh\PandaClient\Exception\PandaException;
use Xabbuh\PandaClient\Model\Video;

/**
 * Command to create an encoding.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class CreateEncodingCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('panda:encoding:create');
        $this->setDescription('Create an encoding');
        $this->addArgument(
            'video-id',
            InputArgument::REQUIRED,
            'Id of the video being encoded'
        );
        $this->addOption(
            'profile-id',
            null,
            InputOption::VALUE_REQUIRED,
            'Id of the profile'
        );
        $this->addOption(
            'profile-name',
            null,
            InputOption::VALUE_REQUIRED,
            'A profile\'s name'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $profileId = $input->getOption('profile-id');
            $profileName = $input->getOption('profile-name');

            if ('' !== $profileId && '' === $profileName) {
                $encoding = $this->getCloud($input)->createEncoding(
                    $input->getArgument('video-id'),
                    $profileId
                );
                $output->writeln(
                    sprintf(
                        'Successfully created encoding with id %s',
                        $encoding->getId()
                    )
                );
            } elseif ($profileId == '' && $profileName != '') {
                $encoding = $this->getCloud($input)->createEncodingWithProfileName(
                    $input->getArgument('video-id'),
                    $profileName
                );
                $output->writeln(
                    sprintf(
                        'Successfully created encoding with id %s',
                        $encoding->getId()
                    )
                );
            } else {
                $output->writeln(
                    'Exactly one option of --profile-id or --profile-name must be given.'
                );
            }
        } catch (PandaException $e) {
            $output->write(
                'An error occurred while trying to delete the encoding: '
            );
            $output->writeln($e->getMessage());
        }
    }
}
