<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
* @author Bartosz Siejka <bartosz.siejka@lakion.com>
*/
class ExportUserReaderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:export:reader:user')
            ->setDescription('Test command for export reader class.')
            ->addArgument(
                'batch_size',
                InputArgument::REQUIRED,
                'Number of rows.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userReader = $this->getContainer()->get('sylius.export.user_reader');
        $userReader->setConfiguration(array('batch_size' => $input->getArgument('batch_size')));
        
        $output->writeln(serialize($userReader->read()));
    }
}