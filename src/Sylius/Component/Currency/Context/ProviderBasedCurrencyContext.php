<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Context;

use Sylius\Component\Currency\Provider\CurrencyProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProviderBasedCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var CurrencyProviderInterface
     */
    private $currencyProvider;

    /**
     * @param CurrencyProviderInterface $currencyProvider
     */
    public function __construct(CurrencyProviderInterface $currencyProvider)
    {
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        $availableCurrenciesCodes = $this->currencyProvider->getAvailableCurrenciesCodes();
        $currencyCode = $this->currencyProvider->getDefaultCurrencyCode();

        if (!in_array($currencyCode, $availableCurrenciesCodes, true)) {
            throw CurrencyNotFoundException::notAvailable($currencyCode, $availableCurrenciesCodes);
        }

        return $currencyCode;
    }
}
