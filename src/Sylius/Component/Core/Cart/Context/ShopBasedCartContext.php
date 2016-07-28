<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Cart\Context;

use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Context\CartNotFoundException;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShopBasedCartContext implements CartContextInterface
{
    /**
     * @var FactoryInterface
     */
    private $cartFactory;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @var CartInterface
     */
    private $cart;

    /**
     * @param FactoryInterface $cartFactory
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(FactoryInterface $cartFactory, ShopperContextInterface $shopperContext)
    {
        $this->cartFactory = $cartFactory;
        $this->shopperContext = $shopperContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        if (null !== $this->cart) {
            return $this->cart;
        }

        /** @var OrderInterface $cart */
        $cart = $this->cartFactory->createNew();

        try {
            $cart->setChannel($this->shopperContext->getChannel());
        } catch (ChannelNotFoundException $exception) {
            throw new CartNotFoundException($exception);
        }

        try {
            $cart->setCurrencyCode($this->shopperContext->getCurrencyCode());
        } catch (CurrencyNotFoundException $exception) {
            throw new CartNotFoundException($exception);
        }

        $cart->setCustomer($this->shopperContext->getCustomer());

        $this->cart = $cart;

        return $cart;
    }
}
