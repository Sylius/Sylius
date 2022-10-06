<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateChannelByQueryStringEvent extends Event
{
    private Proxy|ChannelInterface|null $channel = null;

    public function __construct(private string $queryString)
    {
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getChannel(): Proxy|ProductInterface
    {
        Assert::notNull($this->channel, sprintf('Channel "%s" has not been found or created.', $this->queryString));

        return $this->channel;
    }

    public function setChannel(Proxy|ProductInterface $channel): void
    {
        $this->channel = $channel;
    }
}
