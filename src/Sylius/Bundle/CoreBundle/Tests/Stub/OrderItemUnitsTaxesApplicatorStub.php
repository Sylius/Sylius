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

namespace Sylius\Bundle\CoreBundle\Tests\Stub;

use Sylius\Bundle\CoreBundle\Attribute\AsOrderItemUnitsTaxesApplicator;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;

#[AsOrderItemUnitsTaxesApplicator(priority: 15)]
final class OrderItemUnitsTaxesApplicatorStub implements OrderTaxesApplicatorInterface
{
    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
    }
}
