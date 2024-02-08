<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CurrencyContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RepositoryInterface $currencyRepository,
        private FactoryInterface $currencyFactory,
        private ObjectManager $channelManager,
    ) {
    }

    /**
     * @Given the store has currency :currencyCode
     */
    public function theStoreHasCurrency($currencyCode)
    {
        $currency = $this->createCurrency($currencyCode);

        $this->saveCurrency($currency);
    }

    /**
     * @Given the store has currency :currencyCode, :secondCurrencyCode
     * @Given the store has currency :currencyCode and :secondCurrencyCode
     * @Given the store has currency :currencyCode, :secondCurrencyCode and :thirdCurrencyCode
     */
    public function theStoreHasCurrencyAnd($currencyCode, $secondCurrencyCode, $thirdCurrencyCode = null)
    {
        $this->saveCurrency($this->createCurrency($currencyCode));
        $this->saveCurrency($this->createCurrency($secondCurrencyCode));

        if (null !== $thirdCurrencyCode) {
            $this->saveCurrency($this->createCurrency($thirdCurrencyCode));
        }
    }

    /**
     * @Given the currency :currencyCode has been disabled
     */
    public function theStoreHasDisabledCurrency($currencyCode)
    {
        $currency = $this->provideCurrency($currencyCode);

        $this->saveCurrency($currency);
    }

    /**
     * @Given /^(that channel|"[^"]+" channel)(?: also|) allows to shop using the "([^"]+)" currency$/
     * @Given /^(that channel|"[^"]+" channel)(?: also|) allows to shop using "([^"]+)" and "([^"]+)" currencies$/
     * @Given /^(that channel)(?: also|) allows to shop using "([^"]+)", "([^"]+)" and "([^"]+)" currencies$/
     */
    public function thatChannelAllowsToShopUsingAndCurrencies(ChannelInterface $channel, ...$currenciesCodes)
    {
        foreach ($currenciesCodes as $currencyCode) {
            $channel->addCurrency($this->provideCurrency($currencyCode));
        }

        $this->channelManager->flush();
    }

    private function saveCurrency(CurrencyInterface $currency)
    {
        $this->sharedStorage->set('currency', $currency);
        $this->currencyRepository->add($currency);
    }

    /**
     * @param string $currencyCode
     *
     * @return CurrencyInterface
     */
    private function createCurrency($currencyCode)
    {
        /** @var CurrencyInterface $currency */
        $currency = $this->currencyFactory->createNew();
        $currency->setCode($currencyCode);

        return $currency;
    }

    /**
     * @param string $currencyCode
     *
     * @return CurrencyInterface
     */
    private function provideCurrency($currencyCode)
    {
        $currency = $this->currencyRepository->findOneBy(['code' => $currencyCode]);
        if (null === $currency) {
            /** @var CurrencyInterface $currency */
            $currency = $this->createCurrency($currencyCode);

            $this->currencyRepository->add($currency);
        }

        return $currency;
    }
}
