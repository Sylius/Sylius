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
 * Validator which validates if a province is valid
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ProvinceAddressConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof AddressInterface) {
            throw new \InvalidArgumentException(
                'ProvinceAddressConstraintValidator can only validate instances of "Sylius\Component\Addressing\Model\AddressInterface"'
            );
        }

        $propertyPath = $this->context->getPropertyPath();

        foreach ($this->context->getViolations()->getIterator() as $violation) {
            if (0 === strpos($violation->getPropertyPath(), $propertyPath)) {
                return;
            }
        }

        if (!$this->isProvinceValid($value)) {
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
    protected function isProvinceValid(AddressInterface $address)
    {
        if (null === $address->getCountry()) {
            return false;
        }

        if (!$address->getCountry()->hasProvinces()) {
            return true;
        }

        if (null === $address->getProvince()) {
            return false;
        }

        if ($address->getCountry()->hasProvince($address->getProvince())) {
            return true;
        }

        return false;
    }
}
