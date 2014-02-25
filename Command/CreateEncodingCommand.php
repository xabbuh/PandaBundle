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

            if (null !== $profileId && null === $profileName) {
                $video = new Video();
                $video->setId($input->getArgument('video-id'));
                $encoding = $this->getCloud($input)->createEncodingWithProfileId(
                    $video,
                    $profileId
                );
                $output->writeln(
                    sprintf(
                        '<info>Successfully created encoding with id %s</info>',
                        $encoding->getId()
                    )
                );
            } elseif (null === $profileId && null !== $profileName) {
                $video = new Video();
                $video->setId($input->getArgument('video-id'));
                $encoding = $this->getCloud($input)->createEncodingWithProfileName(
                    $video,
                    $profileName
                );
                $output->writeln(
                    sprintf(
                        '<info>Successfully created encoding with id %s</info>',
                        $encoding->getId()
                    )
                );
            } else {
                $output->writeln(
                    '<error>Exactly one option of --profile-id or --profile-name must be given.</error>'
                );
            }
        } catch (PandaException $e) {
            $output->writeln(
                '<error>An error occurred while trying to delete the encoding: '
                .$e->getMessage().'</error>'
            );
        }
    }
}
