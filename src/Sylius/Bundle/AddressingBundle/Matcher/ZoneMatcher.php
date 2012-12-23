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

use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\AddressingBundle\Entity\ZoneMemberCountry;
use Sylius\Bundle\AddressingBundle\Entity\ZoneMemberProvince;
use Sylius\Bundle\AddressingBundle\Entity\ZoneMemberZone;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

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
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function match(AddressInterface $address)
    {
        foreach ($this->repository->findAll() as $zone) {
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
     * @return bool
     */
    private function addressBelongsToZone(AddressInterface $address, ZoneInterface $zone)
    {
        foreach ($zone->getMembers() as $member) {
            if ($member instanceof ZoneMemberProvince) {
                return null !== $address->getProvince() && $address->getProvince()->getId() === $member->getProvince()->getId();
            } elseif ($member instanceof ZoneMemberCountry) {
                return null !== $address->getCountry() && $address->getCountry()->getId() === $member->getCountry()->getId();
            } elseif ($member instanceof ZoneMemberZone) {
                return $this->addressBelongsToZone($address, $member->getZone());
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Unexpected zone member type "%s".',
                    get_class($member)
                ));
            }
        }
    }
}
