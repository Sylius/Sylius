<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class ResetPasswordTokenExpiresValidator extends ConstraintValidator
{
    public function __construct(
        private UserRepositoryInterface $shopUserRepository,
        private string $passwordResetTokenTtl
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::string($value);

        /** @var ResetPasswordTokenExpires $constraint */
        Assert::isInstanceOf($constraint, ResetPasswordTokenExpires::class);

        /** @var ShopUserInterface|null $shopUser */
        $shopUser = $this->shopUserRepository->findOneBy(['passwordResetToken' => $value]);

        if (null === $shopUser) {
            return;
        }

        $lifetime = new \DateInterval($this->passwordResetTokenTtl);

        if ($shopUser->isPasswordRequestNonExpired($lifetime)) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}
