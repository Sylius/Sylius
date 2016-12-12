<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Resolver;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Matcher\AddressZoneMatcherInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ZoneResolver implements ZoneResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var AddressZoneMatcherInterface
     */
    private $addressZoneResolver;

    /**
     * @var array
     */
    private $priorities;

    /**
     * @param RepositoryInterface $zoneRepository
     * @param AddressZoneMatcherInterface $addressZoneResolver
     * @param array $priorities
     */
    public function __construct(
        RepositoryInterface $zoneRepository,
        AddressZoneMatcherInterface $addressZoneResolver,
        array $priorities
    ) {
        $this->zoneRepository = $zoneRepository;
        $this->addressZoneResolver = $addressZoneResolver;
        $this->priorities = $priorities;
    }

    /**
     * {@inheritdoc}
     */
    public function match(AddressInterface $address, $scope = null)
    {
        $zones = [];

        /* @var ZoneInterface $zone */
        foreach ($this->getZones($scope) as $zone) {
            if ($this->addressZoneResolver->addressBelongsToZone($address, $zone)) {
                $zones[$zone->getType()] = $zone;
            }
        }

        foreach ($this->priorities as $priority) {
            if (isset($zones[$priority])) {
                return $zones[$priority];
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function matchAll(AddressInterface $address, $scope = null)
    {
        $zones = [];

        foreach ($this->getZones($scope) as $zone) {
            if ($this->addressZoneResolver->addressBelongsToZone($address, $zone)) {
                $zones[] = $zone;
            }
        }

        return $zones;
    }

    /**
     * @param string|null $scope
     *
     * @return array
     */
    private function getZones($scope = null)
    {
        if (null === $scope) {
            return $this->zoneRepository->findAll();
        }

        return $this->zoneRepository->findBy(['scope' => $scope]);
    }
}
