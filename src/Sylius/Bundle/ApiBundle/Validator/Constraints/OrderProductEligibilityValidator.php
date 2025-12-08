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

use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderProductEligibilityValidator extends ConstraintValidator
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(private readonly OrderRepositoryInterface $orderRepository)
    {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, CompleteOrder::class);

        /** @var OrderProductEligibility $constraint */
        Assert::isInstanceOf($constraint, OrderProductEligibility::class);

        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->orderTokenValue]);

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var OrderItemInterface[] $orderItems */
        $orderItems = $order->getItems();
        $channel = $order->getChannel();

        foreach ($orderItems as $orderItem) {
            if (!$orderItem->getVariant()->isEnabled()) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('%productName%', $orderItem->getVariant()->getName())
                    ->setCode(OrderProductEligibility::PRODUCT_NOT_ELIGIBLE_ERROR)
                    ->addViolation()
                ;
            } elseif (!$orderItem->getProduct()->isEnabled()) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('%productName%', $orderItem->getProduct()->getName())
                    ->setCode(OrderProductEligibility::PRODUCT_NOT_ELIGIBLE_ERROR)
                    ->addViolation()
                ;
            } elseif (null !== $channel && !$orderItem->getProduct()->hasChannel($channel)) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->setParameter('%productName%', $orderItem->getProduct()->getName())
                    ->setCode(OrderProductEligibility::PRODUCT_NOT_ELIGIBLE_ERROR)
                    ->addViolation()
                ;
            }
        }
    }
}
