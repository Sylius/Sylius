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
class ProvinceAddressConstraintValidator extends ConstraintValidator
{
    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @var RepositoryInterface
     */
    private $provinceRepository;

    /**
     * @param RepositoryInterface $countryRepository
     * @param RepositoryInterface $provinceRepository
     */
    public function __construct(RepositoryInterface $countryRepository, RepositoryInterface $provinceRepository)
    {
        $this->countryRepository = $countryRepository;
        $this->provinceRepository = $provinceRepository;
    }

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

        foreach (iterator_to_array($this->context->getViolations()) as $violation) {
            if (0 === strpos($violation->getPropertyPath(), $propertyPath)) {
                return;
            }
        }

        if (!$this->isProvinceValid($value)) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * @param AddressInterface $address
     *
     * @return bool
     */
    protected function isProvinceValid(AddressInterface $address)
    {
        $countryCode = $address->getCountryCode();
        if (null === $country = $this->countryRepository->findOneBy(['code' => $countryCode])) {
            return true;
        }

        if (!$country->hasProvinces()) {
            return true;
        }

        if (null === $address->getProvinceCode()) {
            return false;
        }

        if (null === $province = $this->provinceRepository->findOneBy(['code' => $address->getProvinceCode()])) {
            return false;
        }

        if ($country->hasProvince($province)) {
            return true;
        }

        return false;
    }
}
