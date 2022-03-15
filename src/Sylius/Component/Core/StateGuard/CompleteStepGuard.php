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

class CompleteStepGuard
{
    /**
     * You can skip shipping step and payment step when order is free AND not require shipping.
     */
    public function isSatisfiedBy(OrderInterface $order): bool
    {
        return $order->isShippingRequired() === false && $order->getTotal() === 0;
    }
}
