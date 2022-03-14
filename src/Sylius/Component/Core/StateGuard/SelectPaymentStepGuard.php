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

namespace Sylius\Component\Core\StateGuard;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Order\Requirements\RequiredNonEmptyCartSpecification;

class SelectPaymentStepGuard implements OrderGuardInterface
{
    /**
     * When payment method is required? When order is not free
     * But should we in any instance block the state change? No
     */
    public function isSatisfiedBy(OrderInterface $order): bool
    {
        return (new RequiredNonEmptyCartSpecification())->isSatisfiedBy($order);
    }
}
