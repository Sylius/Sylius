<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @mixin ModelFactory
 */
trait WithZoneTrait
{
    public function withZone(Proxy|ZoneInterface|string $zone): self
    {
        return $this->addState(['zone' => $zone]);
    }
}
