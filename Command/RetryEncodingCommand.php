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
 * Command to restart an encoding.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * @final
 */
class RetryEncodingCommand extends CloudCommand
{
    protected static $defaultName = 'panda:encoding:retry';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Restart an encoding');
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
        $this->getCloud($input)->retryEncoding($encoding);
        $output->writeln(
            '<info>Successfully restarted encoding with id '.$encodingId
            .'</info>'
        );
    }
}
