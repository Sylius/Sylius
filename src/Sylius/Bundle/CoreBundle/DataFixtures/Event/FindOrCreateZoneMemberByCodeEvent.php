<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateZoneMemberByCodeEvent extends Event
{
    private Proxy|ZoneMemberInterface|null $zoneMember = null;

    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getZoneMember(): Proxy|ZoneMemberInterface
    {
        Assert::notNull($this->zoneMember, sprintf('Zone member "%s" has not been found or created.', $this->code));

        return $this->zoneMember;
    }

    public function setZoneMember(Proxy|ZoneMemberInterface $zoneMember): void
    {
        $this->zoneMember = $zoneMember;
    }
}
