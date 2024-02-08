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

namespace Sylius\Component\Core\Shipping\Checker\Rule;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

abstract class OrderTotalRuleChecker implements RuleCheckerInterface
{
    public function isEligible(ShippingSubjectInterface $subject, array $configuration): bool
    {
        if (!$subject instanceof ShipmentInterface) {
            return false;
        }

        $order = $subject->getOrder();
        if (null === $order) {
            return false;
        }

        $channel = $order->getChannel();
        if (null === $channel) {
            return false;
        }

        $amount = $configuration[$channel->getCode()]['amount'] ?? null;
        if (null === $amount) {
            return false;
        }

        return $this->compare($order->getItemsTotal(), $amount);
    }

    abstract protected function compare(int $total, int $threshold): bool;
}
