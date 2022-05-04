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

class Province implements ProvinceInterface, \Stringable
{
    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $abbreviation;

    /** @var CountryInterface|null */
    protected $country;

    public function __toString(): string
    {
        return (string) $this->getName();
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

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): void
    {
        $this->abbreviation = $abbreviation;
    }

    public function getCountry(): ?CountryInterface
    {
        return $this->country;
    }

    public function setCountry(?CountryInterface $country): void
    {
        $this->country = $country;
    }
}
