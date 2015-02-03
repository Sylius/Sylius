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
use Sylius\Component\ImportExport\Writer\CsvWriter;

/**
* @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
*/
class ExportDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:export')
            ->setDescription('Command for exporting data based on export profile.')
            ->addArgument(
                'code',
                InputArgument::REQUIRED,
                'Code of export profile, which data will be saved.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exportProfile = $this->getContainer()->get('sylius.repository.export_profile')->findByCode($input->getArgument('code'));
        if ($exportProfile === null) {
            throw new \InvalidArgumentException('There is no export profile with given code.');
        }

        $this->getContainer()->get('sylius.import_export.exporter')->export($exportProfile[0]);
    }
}