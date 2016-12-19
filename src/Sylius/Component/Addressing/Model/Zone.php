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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class Zone implements ZoneInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $scope = Scope::ALL;

    /**
     * @var Collection|ZoneMemberInterface[]
     */
    protected $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypes()
    {
        return [self::TYPE_COUNTRY, self::TYPE_PROVINCE, self::TYPE_ZONE];
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setType($type)
    {
        if (!in_array($type, self::getTypes())) {
            throw new \InvalidArgumentException('Wrong zone type supplied.');
        }

        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * {@inheritdoc}
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMembers()
    {
        return !$this->members->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addMember(ZoneMemberInterface $member)
    {
        if (!$this->hasMember($member)) {
            $this->members->add($member);
            $member->setBelongsTo($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeMember(ZoneMemberInterface $member)
    {
        if ($this->hasMember($member)) {
            $this->members->removeElement($member);
            $member->setBelongsTo(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasMember(ZoneMemberInterface $member)
    {
        return $this->members->contains($member);
    }
}
