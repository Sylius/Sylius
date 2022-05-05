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

use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class ShopUserExistsValidator extends ConstraintValidator
{
    public function __construct(
        private CanonicalizerInterface $canonicalizer,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::string($value);

        /** @var ShopUserExists $constraint */
        Assert::isInstanceOf($constraint, ShopUserExists::class);

        $emailCanonical = $this->canonicalizer->canonicalize($value);
        /** @var UserInterface $user */
        $user = $this->userRepository->findOneByEmail($emailCanonical);

        if (null !== $user) {
            return;
        }

        $this->context->addViolation($constraint->message, ['%email%' => $emailCanonical]);
    }
}
