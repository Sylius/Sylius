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

use Sylius\Component\Customer\Model\CustomerInterface;

final class CreateOneCustomer implements CreateOneCustomerInterface
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

    public function withEmail(string $email): self
    {
        return $this->with('email', $email);
    }

    public function withFirstName(string $firstName): self
    {
        return $this->with('firstName', $firstName);
    }

    public function withLastName(string $lastName): self
    {
        return $this->with('lastName', $lastName);
    }

    public function male(): self
    {
        return $this->with('gender', CustomerInterface::MALE_GENDER);
    }

    public function female(): self
    {
        return $this->with('gender', CustomerInterface::FEMALE_GENDER);
    }

    public function withPhoneNumber(string $phoneNumber): self
    {
        return $this->with('phoneNumber', $phoneNumber);
    }

    public function withBirthday(\DateTimeInterface|string $birthday): self
    {
        return $this->with('birthday', $birthday);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
