<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Command;

final class CreateManyShopUsers implements CreateManyShopUsersInterface
{
    public function __construct(private int $min, private ?int $max = null, private array $attributes = [])
    {
    }

    public function with(string $key, mixed $value): self
    {
        $cloned = clone $this;
        $cloned->attributes[$key] = $value;

        return $cloned;
    }

    public function min(): int
    {
        return $this->min;
    }

    public function max(): ?int
    {
        return $this->max;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
