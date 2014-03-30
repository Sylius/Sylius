<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Sylius\Component\Addressing\Model\AddressInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator which validates if an address is shippable
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ShippableAddressConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof AddressInterface) {
            throw new \InvalidArgumentException(
                'ShippableAddressConstraintValidator can only validate instances of "Sylius\Component\Addressing\Model\AddressInterface"'
            );
        }

        $propertyPath = $this->context->getPropertyPath();

        foreach ($this->context->getViolations()->getIterator() as $violation) {
            if (0 === strpos($violation->getPropertyPath(), $propertyPath)) {
                return;
            }
        }

        if (!$this->isShippable($value)) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * Override this method to implement your logic
     *
     * @param AddressInterface $address
     *
     * @return boolean
     */
    protected function isShippable(AddressInterface $address)
    {
        return true;
    }
}
