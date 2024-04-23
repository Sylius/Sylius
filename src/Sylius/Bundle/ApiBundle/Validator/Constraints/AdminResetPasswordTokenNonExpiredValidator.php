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

use Sylius\Bundle\CoreBundle\Message\Admin\Account\ResetPassword;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class AdminResetPasswordTokenNonExpiredValidator extends ConstraintValidator
{
    public function __construct(
        private UserRepositoryInterface $adminUserRepository,
        private string $tokenTtl,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ResetPassword::class);

        /** @var AdminResetPasswordTokenNonExpired $constraint */
        Assert::isInstanceOf($constraint, AdminResetPasswordTokenNonExpired::class);

        /** @var AdminUserInterface|null $user */
        $user = $this->adminUserRepository->findOneBy(['passwordResetToken' => $value->token]);
        if (null === $user) {
            return;
        }

        $lifetime = new \DateInterval($this->tokenTtl);

        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
