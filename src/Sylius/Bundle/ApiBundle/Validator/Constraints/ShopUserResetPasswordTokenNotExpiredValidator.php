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

final class ShopUserResetPasswordTokenNotExpiredValidator extends ConstraintValidator
{
    /**
     * @param UserRepositoryInterface<UserInterface> $userRepository
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private string $passwordResetTokenTtl,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::string($value);

        /** @var ShopUserResetPasswordTokenNotExpired $constraint */
        Assert::isInstanceOf($constraint, ShopUserResetPasswordTokenNotExpired::class);

        /** @var UserInterface|null $user */
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $value]);

        if (null === $user) {
            return;
        }

        $lifetime = new \DateInterval($this->passwordResetTokenTtl);

        if ($user->isPasswordRequestNonExpired($lifetime)) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}
