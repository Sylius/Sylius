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
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;

class ChooseShippingMethod implements OrderTokenValueAwareInterface, SubresourceIdAwareInterface, IriToIdentifierConversionAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /** @var string|null */
    public $shipmentId;

    /**
     * @immutable
     *
     * @var string
     */
    public $shippingMethodCode;

    public function __construct(string $shippingMethodCode)
    {
        $this->shippingMethodCode = $shippingMethodCode;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }

    public function getSubresourceId(): ?string
    {
        return $this->shipmentId;
    }

    public function setSubresourceId(?string $subresourceId): void
    {
        $this->shipmentId = $subresourceId;
    }

    public function getSubresourceIdAttributeKey(): string
    {
        return 'shipmentId';
    }
}
