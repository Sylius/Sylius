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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * This implementation can match addresses against zones by country and province.
 * It also handles sub-zones.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneMatcher implements ZoneMatcherInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Zone matching priorities.
     *
     * @var array
     */
    protected $priorities = array(
        ZoneInterface::TYPE_PROVINCE,
        ZoneInterface::TYPE_COUNTRY,
        ZoneInterface::TYPE_ZONE,
    );

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function match(AddressInterface $address, $scope = null)
    {
        $zones = array();

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
        $zones = array();

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
        switch ($type = $member->getBelongsTo()->getType())
        {
            case ZoneInterface::TYPE_PROVINCE:
                return null !== $address->getProvince() && $address->getProvince() === $member->getCode();

            case ZoneInterface::TYPE_COUNTRY:
                return null !== $address->getCountry() && $address->getCountry() === $member->getCode();

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
            return $this->repository->findAll();
        }

        return $this->repository->findBy(array('scope' => $scope));
    }

    /**
     * @param string $code
     *
     * @return ZoneInterface
     */
    protected function getZoneByCode($code)
    {
        return $this->repository->findOneBy(array('code' => $code));
    }
}
