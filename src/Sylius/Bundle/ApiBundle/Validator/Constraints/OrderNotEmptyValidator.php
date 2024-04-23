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
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderNotEmptyValidator extends ConstraintValidator
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

        /** @var OrderNotEmpty $constraint */
        Assert::isInstanceOf($constraint, OrderNotEmpty::class);

        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if ($order->getItems()->isEmpty()) {
            $this->context->addViolation($constraint->message);
        }
    }
}
