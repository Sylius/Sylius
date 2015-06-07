<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Command;

use Sylius\Component\Currency\Importer\ImporterInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportExchangeRateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:currency:import')
            ->setDescription('Import currencies exchange rates using external database.')
            ->addArgument(
                'importer',
                InputArgument::REQUIRED,
                'Name of the currency importer service.'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Fetching data from external database.');

        $container = $this->getContainer();

        /** @var $importer ImporterInterface */
        $importer = $container->get('sylius.currency_importer.'.$input->getArgument('importer'));
        $importer->import();

        $output->writeln('Saving updated exchange rates.');
    }
}
