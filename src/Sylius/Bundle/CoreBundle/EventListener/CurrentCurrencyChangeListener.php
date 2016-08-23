<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Context\CartNotFoundException;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Core\OrderProcessing\OrderExchangeRateAndCurrencyUpdaterInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CurrentCurrencyChangeListener
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var OrderExchangeRateAndCurrencyUpdaterInterface
     */
    private $exchangeRateAndCurrencyUpdater;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param CartContextInterface $cartContext
     * @param OrderExchangeRateAndCurrencyUpdaterInterface $exchangeRateAndCurrencyUpdater
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        CartContextInterface $cartContext,
        OrderExchangeRateAndCurrencyUpdaterInterface $exchangeRateAndCurrencyUpdater,
        EventDispatcherInterface $dispatcher
    ) {
        $this->cartContext = $cartContext;
        $this->exchangeRateAndCurrencyUpdater = $exchangeRateAndCurrencyUpdater;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param GenericEvent $event
     */
    public function updateCart(GenericEvent $event)
    {
        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException $exception) {
            return;
        }

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, OrderInterface::class);
        }

        $this->exchangeRateAndCurrencyUpdater->update($cart);

        $this->dispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new CartEvent($cart));
    }
}
