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

namespace Sylius\Component\Addressing\Matcher;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;

final class ZoneMatcher implements ZoneMatcherInterface
{
    /**
     * @param ZoneRepositoryInterface<ZoneInterface> $zoneRepository
     */
    public function __construct(private ZoneRepositoryInterface $zoneRepository)
    {
    }

    public function match(AddressInterface $address, ?string $scope = null): ?ZoneInterface
    {
        $zoneByProvince = $this->zoneRepository->findOneByAddressAndType($address, ZoneInterface::TYPE_PROVINCE, $scope);
        if (null !== $zoneByProvince) {
            return $zoneByProvince;
        }

        $zoneByCountry = $this->zoneRepository->findOneByAddressAndType($address, ZoneInterface::TYPE_COUNTRY, $scope);
        if (null !== $zoneByCountry) {
            return $zoneByCountry;
        }

        $zoneByMember = $this->zoneRepository->findOneByAddressAndType($address, ZoneInterface::TYPE_ZONE, $scope);
        if (null !== $zoneByMember) {
            return $zoneByMember;
        }

        return null;
    }

    public function matchAll(AddressInterface $address, ?string $scope = null): array
    {
        $zones = $this->zoneRepository->findByAddress($address);
        $zonesWithParents = $this->getZonesWithParentZones($zones);

        if (null === $scope) {
            return $zonesWithParents;
        }

        return array_filter(
            $zonesWithParents,
            fn (ZoneInterface $zone) => $zone->getScope() === $scope || $zone->getScope() === Scope::ALL,
        );
    }

    /**
     * @param array<ZoneInterface> $zones
     *
     * @return array<ZoneInterface>
     */
    private function getZonesWithParentZones(array $zones): array
    {
        $parentZones = $this->zoneRepository->findByMembers($zones);

        if ([] === $parentZones) {
            return $zones;
        }

        return array_merge($zones, $this->getZonesWithParentZones($parentZones));
    }

    private function addressBelongsToZone(AddressInterface $address, ZoneInterface $zone): bool
    {
        foreach ($zone->getMembers() as $member) {
            if ($this->addressBelongsToZoneMember($address, $member)) {
                return true;
            }
        }

        return false;
    }

    private function addressBelongsToZoneMember(AddressInterface $address, ZoneMemberInterface $member): bool
    {
        switch ($type = $member->getBelongsTo()->getType()) {
            case ZoneInterface::TYPE_PROVINCE:
                return null !== $address->getProvinceCode() && $address->getProvinceCode() === $member->getCode();
            case ZoneInterface::TYPE_COUNTRY:
                return null !== $address->getCountryCode() && $address->getCountryCode() === $member->getCode();
            case ZoneInterface::TYPE_ZONE:
                $zone = $this->getZoneByCode($member->getCode());

                return null !== $zone && $this->addressBelongsToZone($address, $zone);
            default:
                throw new \InvalidArgumentException(sprintf('Unexpected zone type "%s".', $type));
        }
    }

    private function getZoneByCode(string $code): ?ZoneInterface
    {
        return $this->zoneRepository->findOneBy(['code' => $code]);
    }
}
