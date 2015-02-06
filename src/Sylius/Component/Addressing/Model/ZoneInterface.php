<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\GetIdInterface;

/**
 * Zone interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ZoneInterface extends GetIdInterface
{
    const TYPE_COUNTRY = 'country';
    const TYPE_PROVINCE = 'province';
    const TYPE_ZONE = 'zone';

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return ZoneInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return ZoneInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getScope();

    /**
     * @param string $scope
     *
     * @return ZoneInterface
     */
    public function setScope($scope);

    /**
     * @return Collection|ZoneMemberInterface[]
     */
    public function getMembers();

    /**
     * @param Collection $members
     *
     * @return ZoneInterface
     */
    public function setMembers(Collection $members);

    /**
     * @return bool
     */
    public function hasMembers();

    /**
     * @param ZoneMemberInterface $member
     *
     * @return ZoneInterface
     */
    public function addMember(ZoneMemberInterface $member);

    /**
     * @param ZoneMemberInterface $member
     *
     * @return ZoneInterface
     */
    public function removeMember(ZoneMemberInterface $member);

    /**
     * @param ZoneMemberInterface $member
     *
     * @return bool
     */
    public function hasMember(ZoneMemberInterface $member);
}
