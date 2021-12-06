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

namespace Sylius\Component\Core\Resolver;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class TaxationAddressResolver Implements TaxationAddressResolverInterface
{
    /** @var bool */
    private $shippingAddressBasedTaxation;

    public function __construct(bool $shippingAddressBasedTaxation)
    {
        $this->shippingAddressBasedTaxation = $shippingAddressBasedTaxation;
    }

    public function getTaxationAddressFromOrder(OrderInterface $order): ?AddressInterface
    {
        if ($this->shippingAddressBasedTaxation) {
            return $order->getShippingAddress();
        }

        return $order->getBillingAddress();
    }
}
