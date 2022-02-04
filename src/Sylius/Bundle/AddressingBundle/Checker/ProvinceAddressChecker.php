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

namespace Sylius\Bundle\AddressingBundle\Checker;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProvinceAddressChecker implements ProvinceAddressCheckerInterface
{
    private RepositoryInterface $countryRepository;

    private RepositoryInterface $provinceRepository;

    public function __construct(RepositoryInterface $countryRepository, RepositoryInterface $provinceRepository)
    {
        $this->countryRepository = $countryRepository;
        $this->provinceRepository = $provinceRepository;
    }

    public function isValid(AddressInterface $address): bool
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
