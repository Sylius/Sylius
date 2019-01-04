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

namespace Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class ProvinceAddressConstraintValidator extends ConstraintValidator
{
    /** @var RepositoryInterface */
    private $countryRepository;

    /** @var RepositoryInterface */
    private $provinceRepository;

    public function __construct(RepositoryInterface $countryRepository, RepositoryInterface $provinceRepository)
    {
        $this->countryRepository = $countryRepository;
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof AddressInterface) {
            throw new \InvalidArgumentException(
                'ProvinceAddressConstraintValidator can only validate instances of "Sylius\Component\Addressing\Model\AddressInterface"'
            );
        }

        /** @var ProvinceAddressConstraint $constraint */
        Assert::isInstanceOf($constraint, ProvinceAddressConstraint::class);

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

    protected function isProvinceValid(AddressInterface $address): bool
    {
        $countryCode = $address->getCountryCode();

        /** @var CountryInterface|null $country */
        $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

        if (null === $country) {
            return true;
        }

        if (!$country->hasProvinces()) {
            return true;
        }

        if (null === $address->getProvinceCode()) {
            return false;
        }

        /** @var ProvinceInterface|null $province */
        $province = $this->provinceRepository->findOneBy(['code' => $address->getProvinceCode()]);

        if (null === $province) {
            return false;
        }

        if ($country->hasProvince($province)) {
            return true;
        }

        return false;
    }
}
