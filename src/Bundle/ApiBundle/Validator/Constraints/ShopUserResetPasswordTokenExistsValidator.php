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

use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ShopUserResetPasswordTokenExistsValidator extends ConstraintValidator
{
    /**
     * @param UserRepositoryInterface<UserInterface> $shopUserRepository
     */
    public function __construct(private UserRepositoryInterface $shopUserRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::string($value);

        /** @var ShopUserResetPasswordTokenExists $constraint */
        Assert::isInstanceOf($constraint, ShopUserResetPasswordTokenExists::class);

        /** @var UserInterface|null $user */
        $user = $this->shopUserRepository->findOneBy(['passwordResetToken' => $value]);

        if (null !== $user) {
            return;
        }

        $this->context->addViolation($constraint->message, ['%token%' => $value]);
    }
}
