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
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class UserNotFoundValidator extends ConstraintValidator
{
    public function __construct(
        private CanonicalizerInterface $canonicalizer,
        private UserRepositoryInterface $shopUserRepository,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::string($value);

        /** @var UserNotFound $constraint */
        Assert::isInstanceOf($constraint, UserNotFound::class);

        $emailCanonical = $this->canonicalizer->canonicalize($value);
        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->shopUserRepository->findOneByEmail($emailCanonical);

        if (null !== $shopUser) {
            return;
        }

        $this->context->addViolation($constraint->message, ['%email%' => $emailCanonical]);
    }
}
