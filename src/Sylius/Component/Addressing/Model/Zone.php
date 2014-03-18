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
 * Default zone model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Zone implements ZoneInterface
{
    /**
     * Zone id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Zone name.
     *
     * @var string
     */
    protected $name;

    /**
     * Zone type.
     *
     * @var string
     */
    protected $type;

    /**
     * Zone members.
     *
     * @var ArrayCollection
     */
    protected $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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

        return $this;
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
     */
    public function setType($type)
    {
        if (!in_array($type, self::getTypes())) {
            throw new \InvalidArgumentException('Wrong zone type supplied');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Returns all zone types available.
     *
     * @return array of self::TYPE_*
     */
    public static function getTypes()
    {
        return array_keys(self::getTypeChoices());
    }

    /**
     * Used in form choice field.
     *
     * @return array
     */
    public static function getTypeChoices()
    {
        return array(
            self::TYPE_COUNTRY   => 'Country',
            self::TYPE_PROVINCE  => 'Province',
            self::TYPE_ZONE      => 'Zone',
        );
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
    public function setMembers(Collection $members)
    {
        $this->members = $members;

        return $this;
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

        return $this;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMember(ZoneMemberInterface $member)
    {
        return $this->members->contains($member);
    }
}
