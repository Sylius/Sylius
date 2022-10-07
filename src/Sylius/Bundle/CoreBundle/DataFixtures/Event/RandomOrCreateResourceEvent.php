<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class RandomOrCreateResourceEvent extends AbstractResourceEvent
{
    public function __construct(string $factory, array $attributes = [])
    {
        parent::__construct($factory, $attributes);
    }

    public function getResource(): Proxy
    {
        Assert::notNull($this->resource, 'No Resource has been found or created.');

        return $this->resource;
    }
}
