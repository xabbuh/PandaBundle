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
use Xabbuh\PandaClient\Model\Encoding;

/**
 * Command to delete an encoding.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @final
 */
class DeleteEncodingCommand extends CloudCommand
{
    protected static $defaultName = 'panda:encoding:delete';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Delete an encoding');
        $this->addArgument(
            'encoding-id',
            InputArgument::REQUIRED,
            'Id of the encoding'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecuteCommand(InputInterface $input, OutputInterface $output)
    {
        $encodingId = $input->getArgument('encoding-id');
        $encoding = new Encoding();
        $encoding->setId($encodingId);
        $this->getCloud($input)->deleteEncoding($encoding);
        $output->writeln(
            '<info>Successfully deleted encoding with id '.$encodingId.'</info>'
        );
    }
}
