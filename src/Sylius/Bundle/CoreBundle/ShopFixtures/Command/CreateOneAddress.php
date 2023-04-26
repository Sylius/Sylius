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

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Command;

final class CreateOneAddress implements CreateOneAddressInterface
{
    public function __construct(private array $attributes = [])
    {
    }

    public function with(string $key, mixed $value): self
    {
        $cloned = clone $this;
        $cloned->attributes[$key] = $value;

        return $cloned;
    }

    public function withFirstName(string $firstName): self
    {
        return $this->with('firstName', $firstName);
    }

    public function withLastName(string $lastName): self
    {
        return $this->with('lastName', $lastName);
    }

    public function withPhoneNumber(string $phoneNumber): self
    {
        return $this->with('phoneNumber', $phoneNumber);
    }

    public function withCompany(string $company): self
    {
        return $this->with('company', $company);
    }

    public function withStreet(string $street): self
    {
        return $this->with('street', $street);
    }

    public function withCity(string $city): self
    {
        return $this->with('city', $city);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
