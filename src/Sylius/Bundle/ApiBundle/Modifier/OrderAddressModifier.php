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

namespace Sylius\Bundle\ApiBundle\Modifier;

use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderAddressModifier implements OrderAddressModifierInterface
{
    public function __construct(
        private AddressMapperInterface $addressMapper
    ) {
    }

    public function modify(OrderInterface $order, AddressInterface $billingAddress, ?AddressInterface $shippingAddress = null): OrderInterface
    {
        /** @var AddressInterface|null $oldBillingAddress */
        $oldBillingAddress = $order->getBillingAddress();
        /** @var AddressInterface|null $oldShippingAddress */
        $oldShippingAddress = $order->getShippingAddress();

        if ($oldBillingAddress !== null) {
            $order->setBillingAddress($this->addressMapper->mapExisting($oldBillingAddress, $billingAddress));
        } else {
            $order->setBillingAddress($billingAddress);
        }

        $newShippingAddress = $shippingAddress ?? clone $billingAddress;

        if ($oldShippingAddress !== null) {
            $order->setShippingAddress($this->addressMapper->mapExisting($oldShippingAddress, $newShippingAddress));
        } else {
            $order->setShippingAddress($newShippingAddress);
        }

        return $order;
    }
}
