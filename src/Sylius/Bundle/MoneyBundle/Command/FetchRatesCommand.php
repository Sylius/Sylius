<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Command;

use Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\Exception\CurrencyNotExistException;
use Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\ProviderException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FetchRatesCommand
 *
 * Command to fetch exchange rates from external service
 *
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class FetchRatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:money:fetch-rates')
            ->setDescription('Update exchange rates with values from external service. '.
                'External service can be selected in administration settings.')
            ->addArgument('currency', InputArgument::OPTIONAL, 'Update one currency?')
        ;
    }

    /**
     * Update exchange rates
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currency = $input->getArgument('currency');
        $ratesUpdater = $this->getContainer()->get('sylius.exchange_rate.updater');

        $exchangeRepository = $this->getContainer()->get('doctrine')
            ->getRepository('Sylius\Bundle\MoneyBundle\Model\ExchangeRate');

        $baseCurrency = $this->getContainer()->get('sylius.currency_converter')
            ->getBaseCurrency();

        $activeProviderName = $this->getContainer()
            ->get('sylius.exchange_rate.services')
            ->getActiveProviderName();
        ;

        $output->writeln(sprintf("Active Exchange Rate Provider is: <comment>%s</comment>\n",$activeProviderName));

        if ($currency && $currency == $baseCurrency) {
            $output->writeln('Base currency can\'t be updated.');

            return;
        }

        if ($currency) {
            $exchangeRates = $exchangeRepository->findBy(array('currency' => $currency));
        } else {
            $exchangeRates = $exchangeRepository->findAll();
        }

        foreach ($exchangeRates as $exchangeRate) {
            if ($exchangeRate->getCurrency() == $baseCurrency) {
                continue;
            }

            try {
                if (!$ratesUpdater->updateRate($exchangeRate->getCurrency())) {
                    $output->writeln(sprintf("<error>ERROR: Currency %s can't be updated.</error>", $exchangeRate->getCurrency()));
                }
            } catch (CurrencyNotExistException $e) {
                $output->writeln(sprintf("<error>ERROR: %s</error>", $e->getMessage()));
            } catch (ProviderException $e) {
                $output->writeln(sprintf("<error>ERROR: %s</error>", $e->getMessage()));
            }

            $output->writeln(sprintf("<info>%s</info> currency rate is now <question>%g</question>", $exchangeRate->getCurrency(), $exchangeRate->getRate()));
        }
    }
}
