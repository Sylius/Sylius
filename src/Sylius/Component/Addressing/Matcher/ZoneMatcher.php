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
        return $this->zoneRepository->findOneByAddress($address, $scope);
    }

    public function matchAll(AddressInterface $address, ?string $scope = null): array
    {
        $zones = $this->zoneRepository->findAllByAddress($address);
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
        $parentZones = $this->zoneRepository->findZonesByMembers($zones);

        if ([] === $parentZones) {
            return $zones;
        }

        return array_merge($zones, $this->getZonesWithParentZones($parentZones));
    }
}
