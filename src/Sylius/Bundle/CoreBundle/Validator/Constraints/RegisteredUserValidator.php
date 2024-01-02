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

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class RegisteredUserValidator extends ConstraintValidator
{
    public function __construct(private RepositoryInterface $customerRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var CustomerInterface $value */
        Assert::isInstanceOf($value, CustomerInterface::class);

        /** @var RegisteredUser $constraint */
        Assert::isInstanceOf($constraint, RegisteredUser::class);

        /** @var CustomerInterface|null $existingCustomer */
        $existingCustomer = $this->customerRepository->findOneBy(['email' => $value->getEmail()]);
        if (null !== $existingCustomer && null !== $existingCustomer->getUser()) {
            $this->context->buildViolation($constraint->message)->atPath('email')->addViolation();
        }
    }
}
