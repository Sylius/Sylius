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
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class UniqueShopUserEmailValidator extends ConstraintValidator
{
    /** @var CanonicalizerInterface */
    private $canonicalizer;

    /** @var UserRepositoryInterface */
    private $shopUserRepository;

    public function __construct(CanonicalizerInterface $canonicalizer, UserRepositoryInterface $shopUserRepository)
    {
        $this->canonicalizer = $canonicalizer;
        $this->shopUserRepository = $shopUserRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        /** @var UniqueShopUserEmail $constraint */
        Assert::isInstanceOf($constraint, UniqueShopUserEmail::class);

        $emailCanonical = $this->canonicalizer->canonicalize($value);
        $shopUser = $this->shopUserRepository->findOneByEmail($emailCanonical);

        if ($shopUser === null) {
            return;
        }

        $this->context->addViolation($constraint->message);
    }
}
