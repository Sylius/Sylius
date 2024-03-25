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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderProductEligibilityValidator extends ConstraintValidator
{
    /**
     * @throws \InvalidArgumentException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var OrderInterface $value */
        Assert::isInstanceOf($value, OrderInterface::class);

        /** @var OrderProductEligibility $constraint */
        Assert::isInstanceOf($constraint, OrderProductEligibility::class);

        /** @var OrderItemInterface[] $orderItems */
        $orderItems = $value->getItems();

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
