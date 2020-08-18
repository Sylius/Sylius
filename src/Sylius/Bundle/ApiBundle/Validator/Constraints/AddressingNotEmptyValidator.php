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
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AddressingNotEmptyValidator extends ConstraintValidator
{
    /**
     * @var AddressInterface|null $address
     */
    private $address;

    public function __construct(AddressInterface $address) {
        $this->address = $address;
    }

    public function validate($value, Constraint $constraint): void
    {
        $this->context->buildViolation($constraint->message)->addViolation();
    }

}
