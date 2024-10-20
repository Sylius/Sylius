<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Resource\Model\ToggleableTrait;
use Symfony\Component\Intl\Countries;

class Country implements CountryInterface, \Stringable
{
    use ToggleableTrait;

    /** @var mixed */
    protected $id;

    /**
     * Country code ISO 3166-1 alpha-2.
     *
     * @var string|null
     */
    protected $code;

    /** @var Collection<array-key, ProvinceInterface> */
    protected $provinces;

    public function __construct()
    {
        /** @var ArrayCollection<array-key, ProvinceInterface> $this->provinces */
        $this->provinces = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) ($this->getName() ?? $this->getCode());
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

    public function getName(?string $locale = null): ?string
    {
        return $this->code !== null ? Countries::getName($this->code, $locale) : null;
    }

    public function getProvinces(): Collection
    {
        return $this->provinces;
    }

    public function hasProvinces(): bool
    {
        return !$this->provinces->isEmpty();
    }

    public function addProvince(ProvinceInterface $province): void
    {
        if (!$this->hasProvince($province)) {
            $this->provinces->add($province);
            $province->setCountry($this);
        }
    }

    public function removeProvince(ProvinceInterface $province): void
    {
        if ($this->hasProvince($province)) {
            $this->provinces->removeElement($province);
            $province->setCountry(null);
        }
    }

    public function hasProvince(ProvinceInterface $province): bool
    {
        return $this->provinces->contains($province);
    }
}
