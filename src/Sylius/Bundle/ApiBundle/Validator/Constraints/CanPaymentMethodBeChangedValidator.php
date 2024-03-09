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

use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CanPaymentMethodBeChangedValidator extends ConstraintValidator
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ChangePaymentMethod::class);

        Assert::isInstanceOf($constraint, CanPaymentMethodBeChanged::class);

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByTokenValue($value->getOrderTokenValue());
        Assert::notNull($order);

        if ($order->getState() === OrderInterface::STATE_CANCELLED) {
            $this->context->addViolation($constraint::CANNOT_CHANGE_PAYMENT_METHOD_FOR_CANCELLED_ORDER);
        }
    }
}
