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

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

/**
 * Default zone matcher.
 *
 * This implementation can match addresses against zones by country and province.
 * It also handles sub-zones.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ZoneMatcher implements ZoneMatcherInterface
{
    /**
     * Zone repository.
     *
     * @var ObjectRepository
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
     * Constructor.
     *
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function match(AddressInterface $address, $scope = null)
    {
        $zones = array();

        foreach ($this->getZones($scope) as $zone) {
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
     * Checks if address belongs to zone.
     *
     * @param AddressInterface $address
     * @param ZoneInterface    $zone
     *
     * @return Boolean
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
     * Checks if address belongs to particular zone member.
     *
     * @param AddressInterface    $address
     * @param ZoneMemberInterface $member
     *
     * @return Boolean
     *
     * @throws \InvalidArgumentException
     */
    protected function addressBelongsToZoneMember(AddressInterface $address, ZoneMemberInterface $member)
    {
        $type = $member->getBelongsTo()->getType();

        switch ($type) {
            case ZoneInterface::TYPE_PROVINCE:
                return null !== $address->getProvince() && $address->getProvince() === $member->getProvince();

            case ZoneInterface::TYPE_COUNTRY:
                return null !== $address->getCountry() && $address->getCountry() === $member->getCountry();

            case ZoneInterface::TYPE_ZONE:
                return $this->addressBelongsToZone($address, $member->getZone());

            default:
                throw new \InvalidArgumentException(sprintf('Unexpected zone type "%s".', $type));
        }
    }

    /**
     * Gets all zones
     *
     * @param string|null $scope
     *
     * @return array $zones
     */
    protected function getZones($scope = null)
    {
        if (null === $scope) {
            return $this->repository->findAll();
        }

        return $this->repository->findBy(array('scope' => $scope));
    }
}
