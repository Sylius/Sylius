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
use Sylius\Component\Locale\Context\LocaleNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShopBasedCartContext implements CartContextInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @var CartInterface|null
     */
    private $cart;

    /**
     * @param CartContextInterface $cartContext
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(CartContextInterface $cartContext, ShopperContextInterface $shopperContext)
    {
        $this->cartContext = $cartContext;
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
        $cart = $this->cartContext->getCart();

        try {
            $cart->setChannel($this->shopperContext->getChannel());
        } catch (ChannelNotFoundException $exception) {
            throw new CartNotFoundException('Sylius was not able to prepare the cart properly', $exception);
        }

        try {
            $cart->setCurrencyCode($this->shopperContext->getCurrencyCode());
        } catch (CurrencyNotFoundException $exception) {
            throw new CartNotFoundException($exception);
        }

        try {
            $cart->setLocaleCode($this->shopperContext->getLocaleCode());
        } catch (LocaleNotFoundException $exception) {
            throw new CartNotFoundException($exception);
        }

        $cart->setCustomer($this->shopperContext->getCustomer());

        $this->cart = $cart;

        return $cart;
    }
}
