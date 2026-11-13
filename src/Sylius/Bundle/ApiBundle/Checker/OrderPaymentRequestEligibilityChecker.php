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

namespace Sylius\Bundle\ApiBundle\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;

final readonly class OrderPaymentRequestEligibilityChecker implements OrderPaymentRequestEligibilityCheckerInterface
{
    public function isEligible(OrderInterface $order): bool
    {
        return $order->getCheckoutState() === OrderCheckoutStates::STATE_COMPLETED;
    }
}
