<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Matcher;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Addressing\Resolver\AddressZoneResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ZoneMatcher implements ZoneMatcherInterface
{
    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @var AddressZoneResolverInterface
     */
    private $addressZoneResolver;

    /**
     * @var array
     */
    private $priorities = [
        ZoneInterface::TYPE_PROVINCE,
        ZoneInterface::TYPE_COUNTRY,
        ZoneInterface::TYPE_ZONE,
    ];

    /**
     * @param RepositoryInterface $zoneRepository
     * @param AddressZoneResolverInterface $addressZoneResolver
     */
    public function __construct(RepositoryInterface $zoneRepository, AddressZoneResolverInterface $addressZoneResolver)
    {
        $this->zoneRepository = $zoneRepository;
        $this->addressZoneResolver = $addressZoneResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function match(AddressInterface $address, $scope = null)
    {
        $zones = [];

        /* @var ZoneInterface $zone */
        foreach ($availableZones = $this->getZones($scope) as $zone) {
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
