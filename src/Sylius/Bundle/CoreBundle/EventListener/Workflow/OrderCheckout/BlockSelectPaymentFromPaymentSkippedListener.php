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

namespace Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout;

use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionAvailabilityCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Symfony\Component\Workflow\Event\GuardEvent;
use Webmozart\Assert\Assert;

final readonly class BlockSelectPaymentFromPaymentSkippedListener
{
    public function __construct(
        private OrderPaymentMethodSelectionAvailabilityCheckerInterface $orderPaymentMethodSelectionAvailabilityChecker,
    ) {
    }

    public function __invoke(GuardEvent $event): void
    {
        if (!$event->getMarking()->has(OrderCheckoutStates::STATE_PAYMENT_SKIPPED)) {
            return;
        }

        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        if (!$this->orderPaymentMethodSelectionAvailabilityChecker->isPaymentMethodSelectionAvailable($order)) {
            $event->setBlocked(true, 'Payment method selection is not allowed for an order with zero total.');
        }
    }
}
