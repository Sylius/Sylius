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

namespace Sylius\Bundle\ApiBundle\Map;

final class CommandItemIriArgumentToIdentifierMap implements CommandItemIriArgumentToIdentifierMapInterface
{
    /** @psalm-var array<class-string, string> */
    private array $map;

    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /** @psalm-param class-string $className */
    public function get(string $className): string
    {
        return $this->map[$className];
    }

    public function has(?string $className): bool
    {
        return array_key_exists($className, $this->map);
    }
}
