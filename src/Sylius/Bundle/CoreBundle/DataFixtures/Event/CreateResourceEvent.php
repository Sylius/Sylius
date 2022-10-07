<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class CreateResourceEvent extends AbstractResourceEvent
{
    public function getResource(): Proxy
    {
        Assert::notNull($this->resource, 'No resource has been created.');

        return $this->resource;
    }
}
