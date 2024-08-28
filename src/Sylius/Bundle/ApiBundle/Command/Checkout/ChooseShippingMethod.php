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

namespace Sylius\Bundle\ApiBundle\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Command\ShipmentIdAwareInterface;
use Sylius\Bundle\ApiBundle\Command\ShippingMethodCodeAwareInterface;

class ChooseShippingMethod implements OrderTokenValueAwareInterface, ShipmentIdAwareInterface, ShippingMethodCodeAwareInterface, IriToIdentifierConversionAwareInterface
{
    public function __construct(
        protected string $shippingMethodCode,
        protected mixed $shipmentId,
        protected string $orderTokenValue,
    ) {
    }

    public function getShippingMethodCode(): ?string
    {
        return $this->shippingMethodCode;
    }

    public function getShipmentId(): mixed
    {
        return $this->shipmentId;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }
}
