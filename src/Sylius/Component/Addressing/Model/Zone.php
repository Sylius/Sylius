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
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\TaxRateInterface;

class Zone implements ZoneInterface
{
    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $type;

    /** @var string|null */
    protected $scope = Scope::ALL;

    /**
     * @var Collection|ZoneMemberInterface[]
     *
     * @psalm-var Collection<array-key, ZoneMemberInterface>
     */
    protected $members;

    /**
     * @var Collection|ShippingMethodInterface[]
     * 
     * @psalm-var Collection<array-key, ShippingMethodInterface>
     */
    protected $shippingMethods;

    /**
     * @var Collection|TaxRateInterface[]
     * 
     * @psalm-var Collection<array-key, TaxRateInterface>
     */
    protected $taxRates;

    public function __construct()
    {
        /** @var ArrayCollection<array-key, ZoneMemberInterface> $this->members */
        $this->members = new ArrayCollection();

        /** @var ArrayCollection<array-key, ShippingMethodInterface> $this->shippingMethods */
        $this->shippingMethods = new ArrayCollection();

        /** @var ArrayCollection<array-key, TaxRateInterface> $this->taxRates */
        $this->taxRates = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public static function getTypes(): array
    {
        return [self::TYPE_COUNTRY, self::TYPE_PROVINCE, self::TYPE_ZONE];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setType(?string $type): void
    {
        if (!in_array($type, static::getTypes(), true)) {
            throw new \InvalidArgumentException('Wrong zone type supplied.');
        }

        $this->type = $type;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): void
    {
        $this->scope = $scope;
    }

    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function hasMembers(): bool
    {
        return !$this->members->isEmpty();
    }

    public function addMember(ZoneMemberInterface $member): void
    {
        if (!$this->hasMember($member)) {
            $this->members->add($member);
            $member->setBelongsTo($this);
        }
    }

    public function removeMember(ZoneMemberInterface $member): void
    {
        if ($this->hasMember($member)) {
            $this->members->removeElement($member);
            $member->setBelongsTo(null);
        }
    }

    public function hasMember(ZoneMemberInterface $member): bool
    {
        return $this->members->contains($member);
    }

    public function getShippingMethods(): Collection
    {
        return $this->shippingMethods;
    }

    public function hasShippingMethods(): bool
    {
        return !$this->shippingMethods->isEmpty();
    }

    public function addShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        if (!$this->hasShippingMethod($shippingMethod)) {
            $this->shippingMethods->add($shippingMethod);
            $shippingMethod->setZone($this);
        }
    }

    public function removeShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        if ($this->hasShippingMethod($shippingMethod)) {
            $this->shippingMethods->removeElement($shippingMethod);
            $shippingMethod->setZone(null);
        }
    }

    public function hasShippingMethod(ShippingMethodInterface $shippingMethod): bool
    {
        return $this->shippingMethods->contains($shippingMethod);
    }

    public function getTaxRates(): Collection
    {
        return $this->taxRates;
    }

    public function hasTaxRates(): bool
    {
        return !$this->taxRates->isEmpty();
    }

    public function addTaxRate(TaxRateInterface $taxRate): void
    {
        if (!$this->hasTaxRate($taxRate)) {
            $this->taxRates->add($taxRate);
            $taxRate->setZone($this);
        }
    }

    public function removeTaxRate(TaxRateInterface $taxRate): void
    {
        if ($this->hasTaxRate($taxRate)) {
            $this->taxRates->removeElement($taxRate);
            $taxRate->setZone(null);
        }
    }

    public function hasTaxRate(TaxRateInterface $taxRate): bool
    {
        return $this->taxRates->contains($taxRate);
    }
}
