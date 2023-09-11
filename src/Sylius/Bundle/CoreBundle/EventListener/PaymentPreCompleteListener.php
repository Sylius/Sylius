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
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

final class PaymentPreCompleteListener
{
    public function __construct(
        private AvailabilityCheckerInterface $availabilityChecker,
    ) {
    }

    public function checkStockAvailability(ResourceControllerEvent $event): void
    {
        /** @var PaymentInterface $payment */
        $payment = $event->getSubject();
        $orderItems = $payment->getOrder()->getItems();

        foreach ($orderItems as $orderItem) {
            $variant = $orderItem->getVariant();

            if (!$this->availabilityChecker->isStockSufficient($variant, $orderItem->getQuantity())) {
                $event->setMessageType('error');
                $event->setMessage('sylius.resource.payment.cannot_be_completed');
                $event->setMessageParameters(['%productVariantCode%' => $variant->getCode()]);
                $event->stopPropagation();

                break;
            }
        }
    }
}
