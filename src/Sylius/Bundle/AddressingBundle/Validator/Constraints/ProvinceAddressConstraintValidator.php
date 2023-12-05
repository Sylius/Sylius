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
    public function __construct(private RepositoryInterface $countryRepository, private RepositoryInterface $provinceRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof AddressInterface) {
            throw new \InvalidArgumentException(
                'ProvinceAddressConstraintValidator can only validate instances of "Sylius\Component\Addressing\Model\AddressInterface"',
            );
        }

        /** @var ProvinceAddressConstraint $constraint */
        Assert::isInstanceOf($constraint, ProvinceAddressConstraint::class);

        $propertyPath = $this->context->getPropertyPath();

        foreach (iterator_to_array($this->context->getViolations()) as $violation) {
            if (str_starts_with($violation->getPropertyPath(), $propertyPath)) {
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

        if (!$country->hasProvinces() && null !== $address->getProvinceCode()) {
            return false;
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

        return $country->hasProvince($province);
    }
}
