<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Cart\Context;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
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
     * @var OrderInterface|null
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
            /** @var ChannelInterface $channel */
            $channel = $this->shopperContext->getChannel();

            $cart->setChannel($channel);
            $cart->setCurrencyCode($channel->getBaseCurrency()->getCode());
            $cart->setLocaleCode($this->shopperContext->getLocaleCode());
        } catch (ChannelNotFoundException $exception) {
            throw new CartNotFoundException('Sylius was not able to prepare the cart.', $exception);
        } catch (CurrencyNotFoundException $exception) {
            throw new CartNotFoundException('Sylius was not able to prepare the cart.', $exception);
        } catch (LocaleNotFoundException $exception) {
            throw new CartNotFoundException('Sylius was not able to prepare the cart.', $exception);
        }

        /** @var CustomerInterface $customer */
        $customer = $this->shopperContext->getCustomer();
        if (null !== $customer) {
            $this->setCustomerAndAddressOnCart($cart, $customer);
        }

        $this->cart = $cart;

        return $cart;
    }

    /**
     * @param OrderInterface $cart
     * @param CustomerInterface $customer
     */
    private function setCustomerAndAddressOnCart(OrderInterface $cart, CustomerInterface $customer)
    {
        $cart->setCustomer($customer);

        $defaultAddress = $customer->getDefaultAddress();
        if (null !== $defaultAddress) {
            $cart->setShippingAddress(clone $defaultAddress);
        }
    }
}
