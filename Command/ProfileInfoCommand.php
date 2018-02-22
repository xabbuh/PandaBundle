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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Show a profile's details.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ProfileInfoCommand extends CloudCommand
{
    protected static $defaultName = 'panda:profile:info';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Fetch information for a profile');
        $this->addArgument(
            'profile-id',
            InputArgument::REQUIRED,
            'The id of the profile being fetched'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $profile = $this->getCloud($input)->getProfile($input->getArgument('profile-id'));
        $table = new Table($output);
        $table->addRows(array(
            array('id', $profile->getId()),
            array('title', $profile->getTitle()),
            array('name', $profile->getName()),
            array('file extension', $profile->getExtname()),
            array('width', $profile->getWidth()),
            array('height', $profile->getHeight()),
            array('audio bit rate', $profile->getAudioBitrate()),
            array('video bit rate', $profile->getVideoBitrate()),
            array('aspect mode', $profile->getAspectMode()),
            array('created at', $profile->getCreatedAt()),
            array('updated at', $profile->getUpdatedAt()),
        ));
        $table->render($output);
    }
}
