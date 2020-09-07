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

namespace Sylius\Bundle\ApiBundle\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

/** @experimental */
class ChooseShippingMethod implements OrderTokenValueAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /**
     * @psalm-immutable
     *
     * @var string|int
     */
    public $shipmentIdentifier;

    /**
     * @psalm-immutable
     *
     * @var string
     */
    public $shippingMethod;

    /**
     * @param string|int $shipmentIdentifier
     */
    public function __construct($shipmentIdentifier, string $shippingMethod)
    {
        $this->shipmentIdentifier = $shipmentIdentifier;
        $this->shippingMethod = $shippingMethod;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }
}
