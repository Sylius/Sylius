<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Request;

interface ResolveNextRouteInterface
{
    public function getRouteName(): ?string;
    public function setRouteName(string $routeName): void;
    public function getRouteParameters(): array;
    public function setRouteParameters(array $parameters): void;
}
