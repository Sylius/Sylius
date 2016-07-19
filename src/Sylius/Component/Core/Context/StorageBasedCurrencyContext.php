<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class StorageBasedCurrencyContext implements CurrencyContextInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var CurrencyStorageInterface
     */
    private $currencyStorage;

    /**
     * @var CurrencyProviderInterface
     */
    private $currencyProvider;

    /**
     * @param ChannelContextInterface $channelContext
     * @param CurrencyStorageInterface $currencyStorage
     * @param CurrencyProviderInterface $currencyProvider
     */
    public function __construct(
        ChannelContextInterface $channelContext,
        CurrencyStorageInterface $currencyStorage,
        CurrencyProviderInterface $currencyProvider
    ) {
        $this->channelContext = $channelContext;
        $this->currencyStorage = $currencyStorage;
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        $availableCurrenciesCodes = $this->currencyProvider->getAvailableCurrenciesCodes();
        $currencyCode = $this->currencyStorage->get($this->channelContext->getChannel());

        if (!in_array($currencyCode, $availableCurrenciesCodes, true)) {
            throw CurrencyNotFoundException::notAvailable($currencyCode, $availableCurrenciesCodes);
        }

        return $currencyCode;
    }
}
