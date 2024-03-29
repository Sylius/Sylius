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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderProductEligibilityValidator extends ConstraintValidator
{
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, OrderTokenValueAwareInterface::class);

        /** @var OrderProductEligibility $constraint */
        Assert::isInstanceOf($constraint, OrderProductEligibility::class);

        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var OrderItemInterface[] $orderItems */
        $orderItems = $order->getItems();

        foreach ($orderItems as $orderItem) {
            if (!$orderItem->getVariant()->isEnabled()) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%productName%' => $orderItem->getVariant()->getName()],
                );
            } elseif (!$orderItem->getProduct()->isEnabled()) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%productName%' => $orderItem->getProduct()->getName()],
                );
            }
        }
    }
}
