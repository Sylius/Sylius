<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateResourceEvent extends Event
{
    private Proxy|ResourceInterface|null $resource = null;

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

    public function getResource(): Proxy|ResourceInterface
    {
        Assert::notNull($this->resource, 'Resource has not been found or created.');

        return $this->resource;
    }

    public function setResource(Proxy|ResourceInterface $resource): void
    {
        $this->resource = $resource;
    }
}
