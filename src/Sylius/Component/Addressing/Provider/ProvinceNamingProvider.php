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

namespace Sylius\Component\Addressing\Provider;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

class ProvinceNamingProvider implements ProvinceNamingProviderInterface
{
    public function __construct(private RepositoryInterface $provinceRepository)
    {
    }

    public function getName(AddressInterface $address): string
    {
        if (null !== $address->getProvinceName()) {
            return $address->getProvinceName();
        }

        if (null === $address->getProvinceCode()) {
            return '';
        }

        /** @var ProvinceInterface|null $province */
        $province = $this->provinceRepository->findOneBy(['code' => $address->getProvinceCode()]);
        Assert::notNull($province, sprintf('Province with code "%s" not found.', $address->getProvinceCode()));

        return $province->getName();
    }

    public function getAbbreviation(AddressInterface $address): string
    {
        if (null !== $address->getProvinceName()) {
            return $address->getProvinceName();
        }

        if (null === $address->getProvinceCode()) {
            return '';
        }

        /** @var ProvinceInterface|null $province */
        $province = $this->provinceRepository->findOneBy(['code' => $address->getProvinceCode()]);
        Assert::notNull($province, sprintf('Province with code "%s" not found.', $address->getProvinceCode()));

        return $province->getAbbreviation() ?: $province->getName();
    }
}
