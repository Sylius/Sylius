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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Currency\Handler\CurrencyChangeHandlerInterface;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Updater\OrderUpdaterInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Event\CartEvent;
use Sylius\Component\Order\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CartCurrencyChangeHandler implements CurrencyChangeHandlerInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var OrderUpdaterInterface
     */
    private $exchangeRateUpdater;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var EntityManagerInterface
     */
    private $orderManager;

    /**
     * @param CartContextInterface $cartContext
     * @param OrderUpdaterInterface $exchangeRateUpdater
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManagerInterface $orderManager
     */
    public function __construct(
        CartContextInterface $cartContext,
        OrderUpdaterInterface $exchangeRateUpdater,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $orderManager
    ) {
        $this->cartContext = $cartContext;
        $this->exchangeRateUpdater = $exchangeRateUpdater;
        $this->eventDispatcher = $eventDispatcher;
        $this->orderManager = $orderManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($code)
    {
        try {
            /** @var OrderInterface $cart */
            $cart = $this->cartContext->getCart();
            $cart->setCurrencyCode($code);

            $this->exchangeRateUpdater->update($cart);

            $this->orderManager->persist($cart);
            $this->orderManager->flush();

            $this->eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new CartEvent($cart));
        } catch (CartNotFoundException $exception) {
            throw new HandleException(self::class, 'Sylius was unable to find the cart.', $exception);
        }
    }
}
