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

use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class UpdateCartEmailNotAllowedValidator extends ConstraintValidator
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly UserContextInterface $userContext,
    ) {
    }

    /**
     * @param UpdateCart $value
     * @param UpdateCartEmailNotAllowed $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, UpdateCart::class);
        Assert::isInstanceOf($constraint, UpdateCartEmailNotAllowed::class);

        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->orderTokenValue]);

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if ($order->getCustomer() === null) {
            return;
        }

        if ($order->getCustomer()->getEmail() === $value->email) {
            return;
        }

        if ($this->userContext->getUser() !== null) {
            $this->context->addViolation($constraint->message);
        }
    }
}
