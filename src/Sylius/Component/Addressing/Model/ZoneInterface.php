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
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ZoneInterface extends ResourceInterface, CodeAwareInterface
{
    const TYPE_COUNTRY = 'country';
    const TYPE_PROVINCE = 'province';
    const TYPE_ZONE = 'zone';

    /**
     * @return string[]
     */
    public static function getTypes();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getScope();

    /**
     * @param string $scope
     */
    public function setScope($scope);

    /**
     * @return Collection|ZoneMemberInterface[]
     */
    public function getMembers();

    /**
     * @return bool
     */
    public function hasMembers();

    /**
     * @param ZoneMemberInterface $member
     */
    public function addMember(ZoneMemberInterface $member);

    /**
     * @param ZoneMemberInterface $member
     */
    public function removeMember(ZoneMemberInterface $member);

    /**
     * @param ZoneMemberInterface $member
     *
     * @return bool
     */
    public function hasMember(ZoneMemberInterface $member);
}
