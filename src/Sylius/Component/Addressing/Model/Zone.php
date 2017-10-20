<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Zone implements ZoneInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var string|null
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
    public function __toString(): string
    {
        return (string) $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypes(): array
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
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setType(?string $type): void
    {
        if (!in_array($type, static::getTypes(), true)) {
            throw new \InvalidArgumentException('Wrong zone type supplied.');
        }

        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * {@inheritdoc}
     */
    public function setScope(?string $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMembers(): bool
    {
        return !$this->members->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addMember(ZoneMemberInterface $member): void
    {
        if (!$this->hasMember($member)) {
            $this->members->add($member);
            $member->setBelongsTo($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeMember(ZoneMemberInterface $member): void
    {
        if ($this->hasMember($member)) {
            $this->members->removeElement($member);
            $member->setBelongsTo(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasMember(ZoneMemberInterface $member): bool
    {
        return $this->members->contains($member);
    }
}
