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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Inventory\Checker\OrderItemAvailabilityCheckerInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class PaymentPreCompleteListener
{
    public function __construct(
        private OrderItemAvailabilityCheckerInterface $orderItemAvailabilityChecker,
    ) {
    }

    public function checkStockAvailability(ResourceControllerEvent $event): void
    {
        /** @var PaymentInterface $payment */
        $payment = $event->getSubject();
        $orderItems = $payment->getOrder()->getItems();

        foreach ($orderItems as $orderItem) {
            if (!$this->orderItemAvailabilityChecker->isReservedStockSufficient($orderItem)) {
                $variant = $orderItem->getVariant();

                $event->setMessageType('error');
                $event->setMessage('sylius.resource.payment.cannot_be_completed');
                $event->setMessageParameters(['%productVariantCode%' => $variant->getCode()]);
                $event->stopPropagation();

                break;
            }
        }
    }
}
