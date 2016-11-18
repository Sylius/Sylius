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
use Sylius\Component\Order\Processor\OrderProcessorInterface;

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
    private $orderExchangeRateUpdater;

    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    /**
     * @var EntityManagerInterface
     */
    private $orderManager;

    /**
     * @param CartContextInterface $cartContext
     * @param OrderUpdaterInterface $orderExchangeRateUpdater
     * @param OrderProcessorInterface $orderProcessor
     * @param EntityManagerInterface $orderManager
     */
    public function __construct(
        CartContextInterface $cartContext,
        OrderUpdaterInterface $orderExchangeRateUpdater,
        OrderProcessorInterface $orderProcessor,
        EntityManagerInterface $orderManager
    ) {
        $this->cartContext = $cartContext;
        $this->orderExchangeRateUpdater = $orderExchangeRateUpdater;
        $this->orderProcessor = $orderProcessor;
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

            $this->orderExchangeRateUpdater->update($cart);

            $this->orderProcessor->process($cart);

            $this->orderManager->persist($cart);
            $this->orderManager->flush();
        } catch (CartNotFoundException $exception) {
            throw new HandleException(self::class, 'Sylius was unable to find the cart.', $exception);
        }
    }
}
