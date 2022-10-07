<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Zenstruck\Foundry\Proxy;

abstract class AbstractResourceEvent extends Event implements ResourceEventInterface
{
    protected ?Proxy $resource = null;

    public function __construct(private string $factory, private array $attributes)
    {
    }

    public function getFactory(): string
    {
        return $this->factory;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setResource(Proxy $resource): void
    {
        $this->resource = $resource;
    }
}
