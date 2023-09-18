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

namespace Sylius\Component\Core\Cart\Context;

use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Cart\Resolver\CreatedByGuestFlagResolverInterface;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Symfony\Contracts\Service\ResetInterface;
use Webmozart\Assert\Assert;

final class ShopBasedCartContext implements CartContextInterface, ResetInterface
{
    private CartContextInterface $cartContext;

    private ShopperContextInterface $shopperContext;

    private ?CreatedByGuestFlagResolverInterface $createdByGuestFlagResolver;

    private ?OrderInterface $cart = null;

    public function __construct(
        CartContextInterface $cartContext,
        ShopperContextInterface $shopperContext,
        ?CreatedByGuestFlagResolverInterface $createdByGuestFlagResolver = null,
    ) {
        $this->cartContext = $cartContext;
        $this->shopperContext = $shopperContext;
        $this->createdByGuestFlagResolver = $createdByGuestFlagResolver;

        if ($createdByGuestFlagResolver === null) {
            trigger_deprecation(
                'sylius/core',
                '1.10.9',
                'Not passing createdByGuestFlagResolver through constructor is deprecated in Sylius 1.10.9 and it will be prohibited in Sylius 2.0',
            );
        }
    }

    public function getCart(): BaseOrderInterface
    {
        if (null !== $this->cart) {
            return $this->cart;
        }

        $cart = $this->cartContext->getCart();

        /** @var OrderInterface $cart */
        Assert::isInstanceOf($cart, OrderInterface::class);

        try {
            /** @var ChannelInterface $channel */
            $channel = $this->shopperContext->getChannel();

            $cart->setChannel($channel);
            $cart->setCurrencyCode($channel->getBaseCurrency()->getCode());
            $cart->setLocaleCode($this->shopperContext->getLocaleCode());
        } catch (ChannelNotFoundException | CurrencyNotFoundException | LocaleNotFoundException $exception) {
            throw new CartNotFoundException('Sylius was not able to prepare the cart.', $exception);
        }

        /** @var CustomerInterface|null $customer */
        $customer = $this->shopperContext->getCustomer();
        if (null !== $customer) {
            $this->setCustomerAndAddressOnCart($cart, $customer);
        }

        $this->cart = $cart;

        return $cart;
    }

    public function reset(): void
    {
        $this->cart = null;
    }

    private function setCustomerAndAddressOnCart(OrderInterface $cart, CustomerInterface $customer): void
    {
        $this->setCustomer($cart, $customer);

        $defaultAddress = $customer->getDefaultAddress();
        if (null !== $defaultAddress) {
            $clonedAddress = clone $defaultAddress;
            $clonedAddress->setCustomer(null);
            $cart->setBillingAddress($clonedAddress);
        }
    }

    private function setCustomer(OrderInterface $cart, CustomerInterface $customer): void
    {
        if ($this->createdByGuestFlagResolver !== null && !$this->createdByGuestFlagResolver->resolveFlag()) {
            $cart->setCustomerWithAuthorization($customer);

            return;
        }

        $cart->setCustomer($customer);
    }
}
