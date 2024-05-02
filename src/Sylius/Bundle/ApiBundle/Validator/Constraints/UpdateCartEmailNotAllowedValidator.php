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

use Sylius\Bundle\ApiBundle\Command\CustomerEmailAwareInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class UpdateCartEmailNotAllowedValidator extends ConstraintValidator
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private UserContextInterface $userContext,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, OrderTokenValueAwareInterface::class);
        Assert::isInstanceOf($value, CustomerEmailAwareInterface::class);

        /** @var UpdateCartEmailNotAllowed $constraint */
        Assert::isInstanceOf($constraint, UpdateCartEmailNotAllowed::class);

        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $user = $this->userContext->getUser();

        if ($user !== null && $value->getEmail()) {
            $this->context->addViolation($constraint->message);
        }
    }
}
