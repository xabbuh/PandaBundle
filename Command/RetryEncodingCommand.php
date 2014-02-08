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
use Xabbuh\PandaClient\Model\Encoding;

/**
 * Command to restart an encoding.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class RetryEncodingCommand extends CloudCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('panda:encoding:retry');
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
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $encodingId = $input->getArgument('encoding-id');
            $encoding = new Encoding();
            $encoding->setId($encodingId);
            $this->getCloud($input)->retryEncoding($encoding);
            $output->writeln(
                '<info>Successfully restarted encoding with id '.$encodingId
                .'</info>'
            );
        } catch (PandaException $e) {
            $output->writeln(
                '<error>An error occurred while trying to restart the encoding: '
                .$e->getMessage().'</error>'
            );
        }
    }
}
