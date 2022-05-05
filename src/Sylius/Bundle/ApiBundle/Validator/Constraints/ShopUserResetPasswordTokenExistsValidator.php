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

use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class ShopUserResetPasswordTokenExistsValidator extends ConstraintValidator
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::string($value);

        /** @var ShopUserResetPasswordTokenExists $constraint */
        Assert::isInstanceOf($constraint, ShopUserResetPasswordTokenExists::class);

        /** @var UserInterface|null $user */
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $value]);

        if (null !== $user) {
            return;
        }

        $this->context->addViolation($constraint->message, ['%token%' => $value]);
    }
}
