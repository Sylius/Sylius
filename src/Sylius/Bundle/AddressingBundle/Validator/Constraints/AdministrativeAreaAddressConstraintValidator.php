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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AdministrativeAreaAddressConstraintValidator extends ConstraintValidator
{
    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(RepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof AddressInterface) {
            throw new \InvalidArgumentException(
                'AdministrativeAreaAddressConstraintValidator can only validate instances of "Sylius\Component\Addressing\Model\AddressInterface"'
            );
        }

        $propertyPath = $this->context->getPropertyPath();

        foreach (iterator_to_array($this->context->getViolations()) as $violation) {
            if (0 === strpos($violation->getPropertyPath(), $propertyPath)) {
                return;
            }
        }

        if (!$this->isAdministrativeAreaValid($value)) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * @param AddressInterface $address
     *
     * @return bool
     */
    protected function isAdministrativeAreaValid(AddressInterface $address)
    {
        $countryCode = $address->getCountry();
        if (null === $country = $this->countryRepository->findOneBy(array('code' => $countryCode))) {
            return true;
        }

        if (!$country->hasAdministrativeAreas()) {
            return true;
        }

        if (null === $address->getAdministrativeArea()) {
            return false;
        }

        if ($country->hasAdministrativeArea($address->getAdministrativeArea())) {
            return true;
        }

        return false;
    }
}
