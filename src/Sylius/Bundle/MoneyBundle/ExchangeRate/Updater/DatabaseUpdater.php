<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\ExchangeRate\Updater;

use Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\ProviderFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sylius\Bundle\MoneyBundle\Model\ExchangeRateInterface;

/**
 * Class DatabaseUpdater
 *
 * Updates exchange rates using external exchange rate providers
 *
 * @author Ivan Đurđevac <djurdjevac@gmail.com>
 */
class DatabaseUpdater implements UpdaterInterface, ContainerAwareInterface
{
    /**
     * Container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\ProviderInterface
     */
    private $exchangeRateProvider;

    /**
     * Set Container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Create Updater with provider
     */
    public function __construct(ProviderFactory $providerFactory)
    {
        $this->exchangeRateProvider = $providerFactory->createProvider();
    }

    /**
     * Update rate in database for currency
     *
     * @param string $currency
     *
     * @return bool
     */
    public function updateRate($currency)
    {
        $doctrine = $this->container->get('doctrine');
        $exchangeRate = $doctrine
            ->getRepository('Sylius\Bundle\MoneyBundle\Model\ExchangeRate')
            ->findOneBy(array('currency' => $currency));

        $result = $this->fetchRate($exchangeRate);

        $doctrine->getManager()->flush();

        return $result;
    }

    /**
     * Update All rates
     *
     * @return bool
     */
    public function updateAllRates()
    {
        $doctrine = $this->container->get('doctrine');
        $exchangeRates = $doctrine
            ->getRepository('Sylius\Bundle\MoneyBundle\Model\ExchangeRate')
            ->findAll();

        foreach ($exchangeRates as $exchangeRate) {
            $this->fetchRate($exchangeRate);
        }

        $doctrine->getManager()->flush();

        return true;
    }

    /**
     * Fetch rate from external services
     *
     * @param ExchangeRateInterface $exchangeRate
     *
     * @return bool
     */
    private function fetchRate(ExchangeRateInterface $exchangeRate)
    {
        $baseCurrency = $this->getBaseCurrency();

        if ($baseCurrency == $exchangeRate->getCurrency()
        ) {
            return false;
        }

        $currencyRate = $this->exchangeRateProvider->getRate($baseCurrency, $exchangeRate->getCurrency());
        $exchangeRate->setRate($currencyRate);

        return true;
    }

    /**
     * Get base currency
     *
     * @return string
     */
    private function getBaseCurrency()
    {
        $currencyConverter = $this->container->get('sylius.currency_converter');

        return $currencyConverter->getBaseCurrency();
    }
}
