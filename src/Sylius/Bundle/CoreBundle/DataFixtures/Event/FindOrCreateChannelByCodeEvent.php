<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Webmozart\Assert\Assert;
use Zenstruck\Foundry\Proxy;

final class FindOrCreateChannelByCodeEvent extends Event
{
    private Proxy|ChannelInterface|null $channel = null;

    public function __construct(private string $code)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getChannel(): Proxy|ProductInterface
    {
        Assert::notNull($this->channel, sprintf('Channel "%s" has not been found or created.', $this->code));

        return $this->channel;
    }

    public function setChannel(Proxy|ProductInterface $channel): void
    {
        $this->channel = $channel;
    }
}
