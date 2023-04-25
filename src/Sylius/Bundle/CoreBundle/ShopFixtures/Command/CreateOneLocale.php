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

final class CreateOneLocale implements CreateOneLocaleInterface
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

    public function withCode(string $code): self
    {
        return $this->with('code', $code);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
