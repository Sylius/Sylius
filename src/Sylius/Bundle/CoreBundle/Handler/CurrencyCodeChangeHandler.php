<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Handler;

use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\OrderProcessing\OrderExchangeRateAndCurrencyUpdaterInterface;
use Sylius\Component\Core\Updater\OrderUpdaterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CurrencyCodeChangeHandler implements CodeChangeHandlerInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var CurrencyStorageInterface
     */
    private $currencyStorage;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var OrderUpdaterInterface
     */
    private $exchangeRateAndCurrencyUpdater;

    /**
     * @param CartContextInterface $cartContext
     * @param ChannelContextInterface $channelContext
     * @param CurrencyStorageInterface $currencyStorage
     * @param EventDispatcherInterface $eventDispatcher
     * @param OrderUpdaterInterface $exchangeRateAndCurrencyUpdater
     */
    public function __construct(
        CartContextInterface $cartContext,
        ChannelContextInterface $channelContext,
        CurrencyStorageInterface $currencyStorage,
        EventDispatcherInterface $eventDispatcher,
        OrderUpdaterInterface $exchangeRateAndCurrencyUpdater
    ) {
        $this->cartContext = $cartContext;
        $this->channelContext = $channelContext;
        $this->currencyStorage = $currencyStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->exchangeRateAndCurrencyUpdater = $exchangeRateAndCurrencyUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($code)
    {
        $this->currencyStorage->set($this->channelContext->getChannel(), $code);

        $cart = $this->cartContext->getCart();
        $this->exchangeRateAndCurrencyUpdater->update($cart);

        $this->eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new CartEvent($cart));
    }
}
