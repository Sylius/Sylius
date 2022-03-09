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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class OrderItemAvailabilityValidator extends ConstraintValidator
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private AvailabilityCheckerInterface $availabilityChecker
    ) {
    }

    public function validate($value, Constraint $constraint)
    {
        Assert::isInstanceOf($value, OrderTokenValueAwareInterface::class);

        /** @var OrderItemAvailability $constraint */
        Assert::isInstanceOf($constraint, OrderItemAvailability::class);

        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();
            if (!$this->availabilityChecker->isStockSufficient($variant, $orderItem->getQuantity())) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%productVariantName%' => $variant->getName()]
                );
            }
        }
    }
}
