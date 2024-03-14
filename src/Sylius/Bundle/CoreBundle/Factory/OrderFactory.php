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

namespace Sylius\Bundle\CoreBundle\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @implements OrderFactoryInterface<OrderInterface>
 */
final class OrderFactory implements OrderFactoryInterface
{
    public function __construct(
        private FactoryInterface $decoratedFactory,
    ) {
    }

    public function createNew(): OrderInterface
    {
        /** @var OrderInterface $order */
        $order = $this->decoratedFactory->createNew();

        return $order;
    }

    public function createNewCart(
        ChannelInterface $channel,
        ?CustomerInterface $customer,
        string $localeCode,
        ?string $tokenValue = null,
    ): OrderInterface {
        $cart = $this->createNew();
        $cart->setState(OrderInterface::STATE_CART);
        $cart->setChannel($channel);
        $cart->setLocaleCode($localeCode);
        $cart->setCurrencyCode($channel->getBaseCurrency()->getCode());

        if (null !== $tokenValue) {
            $cart->setTokenValue($tokenValue);
        }

        if (null !== $customer) {
            $cart->setCustomerWithAuthorization($customer);
            $cart->setBillingAddress($this->getDefaultAddress($customer));
        }

        return $cart;
    }

    private function getDefaultAddress(CustomerInterface $customer): ?AddressInterface
    {
        $defaultAddress = $customer->getDefaultAddress();
        if (null !== $defaultAddress) {
            $clonedAddress = clone $defaultAddress;
            $clonedAddress->setCustomer(null);

            return $clonedAddress;
        }

        return null;
    }
}
