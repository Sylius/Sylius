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

namespace Sylius\Bundle\PayumBundle\Request;

interface ResolveNextRouteInterface
{
    public function getRouteName(): ?string;

    public function setRouteName(string $routeName): void;

    public function getRouteParameters(): array;

    public function setRouteParameters(array $parameters): void;
}
