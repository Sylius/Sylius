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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Customer\Repository\CustomerGroupRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class CustomerGroupCodeExistsValidator extends ConstraintValidator
{
    /** @param CustomerGroupRepositoryInterface<CustomerGroupInterface> $customerGroupRepository */
    public function __construct(private CustomerGroupRepositoryInterface $customerGroupRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CustomerGroupCodeExists) {
            throw new UnexpectedTypeException($constraint, CustomerGroupCodeExists::class);
        }

        if (empty($value)) {
            return;
        }

        if ($this->customerGroupRepository->findOneBy(['code' => $value]) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->addViolation()
            ;
        }
    }
}
