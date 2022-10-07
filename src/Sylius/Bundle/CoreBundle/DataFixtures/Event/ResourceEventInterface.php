<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Zenstruck\Foundry\Proxy;

interface ResourceEventInterface extends StoppableEventInterface
{
    public function getFactory(): string;

    public function getAttributes(): array;

    public function getResource(): Proxy;

    public function setResource(Proxy $resource): void;
}
