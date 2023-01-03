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

namespace Sylius\Component\Addressing\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CountryProvincesDeletionChecker implements CountryProvincesDeletionCheckerInterface
{
    public function __construct(
        private RepositoryInterface $zoneMemberRepository,
        private RepositoryInterface $provinceRepository,
    ) {
    }

    public function isDeletable(CountryInterface $country): bool
    {
        /** @var ProvinceInterface[] $provinces */
        $provinces = $this->provinceRepository->findBy(['country' => $country]);

        $countryProvincesCodes = $country->getProvinces()
            ->map(fn ($province): string => $province->getCode())
            ->getValues()
        ;

        $provincesCodes = (new ArrayCollection($provinces))
            ->map(fn ($province): string => $province->getCode())
            ->getValues()
        ;

        $provincesCodesToDelete = array_diff($provincesCodes, $countryProvincesCodes);

        $zoneMember = $this->zoneMemberRepository->findOneBy(['code' => $provincesCodesToDelete]);

        return null === $zoneMember;
    }
}
