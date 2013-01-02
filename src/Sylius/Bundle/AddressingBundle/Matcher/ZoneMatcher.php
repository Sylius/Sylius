<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Matcher;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface;

/**
 * Default zone matcher.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
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
    public function match(AddressInterface $address)
    {
        foreach ($this->getZones() as $zone) {
            if ($this->addressBelongsToZone($address, $zone)) {
                return $zone;
            }
        }
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
     * @param AddressInterface $address
     * @param ZoneInterface    $zone
     *
     * @return Boolean
     */
    protected function addressBelongsToZoneMember(AddressInterface $address, ZoneMemberInterface $member)
    {
        switch ($member->getBelongsTo()->getType()) {
            case ZoneInterface::TYPE_PROVINCE:
                return null !== $address->getProvince() && $address->getProvince() === $member->getProvince();
            break;

            case ZoneInterface::TYPE_COUNTRY:
                return null !== $address->getCountry() && $address->getCountry() === $member->getCountry();
            break;

            case ZoneInterface::TYPE_ZONE:
                return $this->addressBelongsToZone($address, $member->getZone());
            break;

            default:
                throw new \InvalidArgumentException(sprintf(
                    'Unexpected zone type "%s".',
                    $zone->getType()
                ));
            break;
        }
    }

    /**
     * Gets zones sorted by priority/type (province, country, zone).
     *
     * @return array $zones
     */
    protected function getZones()
    {
        $priorities = array(
            ZoneInterface::TYPE_PROVINCE => 2,
            ZoneInterface::TYPE_COUNTRY  => 1,
            ZoneInterface::TYPE_ZONE     => 0,
        );

        $zones = $this->repository->findAll();
        usort($zones, function(ZoneInterface $zone1, ZoneInterface $zone2) use ($priorities) {
            if($priorities[$zone1->getType()] > $priorities[$zone2->getType()]) {
                return -1;
            }

            return 1;
        });

        return $zones;
    }
}
