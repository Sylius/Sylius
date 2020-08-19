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

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AddressingNotEmptyValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint): void
    {
        if($value->getFirstName() === '') {
            $this->context
                ->buildViolation("First Name cannot be empty.")
                ->addViolation();
        }
        if($value->getLastName() === '') {
            $this->context
                ->buildViolation("Last Name cannot be empty.")
                ->addViolation();
        }
        if($value->getCity() === '') {
            $this->context
                ->buildViolation("City cannot be empty.")
                ->addViolation();
        }
        if($value->getStreet() === '') {
            $this->context
                ->buildViolation("Street cannot be empty.")
                ->addViolation();
        }

    }

}
