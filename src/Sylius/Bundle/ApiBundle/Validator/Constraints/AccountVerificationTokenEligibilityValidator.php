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

use Sylius\Bundle\ApiBundle\Command\Account\VerifyCustomerAccount;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class AccountVerificationTokenEligibilityValidator extends ConstraintValidator
{
    public function __construct(private RepositoryInterface $shopUserRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, VerifyCustomerAccount::class);

        /** @var AccountVerificationTokenEligibility $constraint */
        Assert::isInstanceOf($constraint, AccountVerificationTokenEligibility::class);

        /** @var UserInterface|null $user */
        $user = $this->shopUserRepository->findOneBy(['emailVerificationToken' => $value->token]);

        if (null === $user) {
            $this->context->addViolation(
                $constraint->message,
                ['%verificationToken%' => $value->token],
            );
        }
    }
}
