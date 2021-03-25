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

use Sylius\Bundle\ApiBundle\Command\ResendVerificationEmail;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class ShopUserNotVerifiedValidator extends ConstraintValidator
{
    /** @var UserRepositoryInterface */
    private $shopUserRepository;

    public function __construct(UserRepositoryInterface $shopUserRepository)
    {
        $this->shopUserRepository = $shopUserRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ResendVerificationEmail::class);

        /** @var ShopUserNotVerified $constraint */
        Assert::isInstanceOf($constraint, ShopUserNotVerified::class);

        $shopUser = $this->shopUserRepository->findOneByEmail($value->email);

        Assert::notNull($shopUser);

        if (!$shopUser->isVerified()) {
            return;
        }

        $this->context->addViolation($constraint->message, ['%email%' => $value->email]);
    }
}
