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

use Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ShopUserNotVerifiedValidator extends ConstraintValidator
{
    public function __construct(private UserRepositoryInterface $shopUserRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ShopUserIdAwareInterface::class);

        /** @var ShopUserNotVerified $constraint */
        Assert::isInstanceOf($constraint, ShopUserNotVerified::class);

        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->shopUserRepository->find($value->getShopUserId());

        Assert::notNull($shopUser);

        if (!$shopUser->isVerified()) {
            return;
        }

        $this->context->addViolation($constraint->message, ['%email%' => $shopUser->getEmail()]);
    }
}
