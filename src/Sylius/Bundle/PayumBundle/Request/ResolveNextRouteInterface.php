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

namespace Sylius\Bundle\PayumBundle\Request;

interface ResolveNextRouteInterface
{
    public function getRouteName(): ?string;

    public function setRouteName(string $routeName): void;

    /** @return array<string, mixed> */
    public function getRouteParameters(): array;

    /** @param array<string, mixed> $parameters */
    public function setRouteParameters(array $parameters): void;
}
