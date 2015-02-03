<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Sylius\Component\ImportExport\Reader\CsvReader;

/**
* @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
*/
class ImportDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:import')
            ->setDescription('Command for importing data based on given data.')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to file that will be imported.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('file');
        
        $reader = new CsvReader();
        $reader->setConfiguration(array('file' => $filePath, 'delimiter' => ';', 'enclosure' => '*', 'headers' => false));
        print_r($reader->read());
    }
}