<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Zenstruck\Foundry\Proxy;

interface WithZoneInterface
{
    /**
     * @return $this
     */
    public function withZone(Proxy|ZoneInterface|string $zone): self;
}
