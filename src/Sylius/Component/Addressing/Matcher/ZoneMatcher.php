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
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneMatcher implements ZoneMatcherInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $zoneRepository;

    /**
     * @var array
     */
    protected $priorities = [
        ZoneInterface::TYPE_PROVINCE,
        ZoneInterface::TYPE_COUNTRY,
        ZoneInterface::TYPE_ZONE,
    ];

    /**
     * @param RepositoryInterface $zoneRepository
     */
    public function __construct(RepositoryInterface $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function match(AddressInterface $address, $scope = null)
    {
        $zones = [];

        /* @var ZoneInterface $zone */
        foreach ($availableZones = $this->getZones($scope) as $zone) {
            if ($this->addressBelongsToZone($address, $zone)) {
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
            if ($this->addressBelongsToZone($address, $zone)) {
                $zones[] = $zone;
            }
        }

        return $zones;
    }

    /**
     * @param AddressInterface $address
     * @param ZoneInterface    $zone
     *
     * @return bool
     */
    protected function addressBelongsToZone(AddressInterface $address, ZoneInterface $zone)
    {
        foreach ($zone->getMembers() as $member) {
            if ($this->addressBelongsToZoneMember($address, $member)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param AddressInterface    $address
     * @param ZoneMemberInterface $member
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    protected function addressBelongsToZoneMember(AddressInterface $address, ZoneMemberInterface $member)
    {
        switch ($type = $member->getBelongsTo()->getType()) {
            case ZoneInterface::TYPE_PROVINCE:
                return null !== $address->getProvinceCode() && $address->getProvinceCode() === $member->getCode();

            case ZoneInterface::TYPE_COUNTRY:
                return null !== $address->getCountryCode() && $address->getCountryCode() === $member->getCode();

            case ZoneInterface::TYPE_ZONE:
                $zone = $this->getZoneByCode($member->getCode());

                return $this->addressBelongsToZone($address, $zone);

            default:
                throw new \InvalidArgumentException(sprintf('Unexpected zone type "%s".', $type));
        }
    }

    /**
     * @param string|null $scope
     *
     * @return array
     */
    protected function getZones($scope = null)
    {
        if (null === $scope) {
            return $this->zoneRepository->findAll();
        }

        return $this->zoneRepository->findBy(['scope' => [$scope, Scope::ALL]]);
    }

    /**
     * @param string $code
     *
     * @return ZoneInterface
     */
    protected function getZoneByCode($code)
    {
        return $this->zoneRepository->findOneBy(['code' => $code]);
    }
}
