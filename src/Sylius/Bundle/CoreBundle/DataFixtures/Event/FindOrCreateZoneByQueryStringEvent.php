<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateZoneByQueryStringEvent extends Event
{
    private Proxy|ZoneInterface|null $zone = null;

    public function __construct(private string $queryString)
    {
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getZone(): Proxy|ZoneInterface
    {
        Assert::notNull($this->zone, sprintf('Zone "%s" has not been found or created.', $this->queryString));

        return $this->zone;
    }

    public function setZone(Proxy|ZoneInterface $zone): void
    {
        $this->zone = $zone;
    }
}
