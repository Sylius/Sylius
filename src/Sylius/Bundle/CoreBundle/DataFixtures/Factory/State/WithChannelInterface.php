<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Sylius\Component\Core\Model\ChannelInterface;
use Zenstruck\Foundry\Proxy;

interface WithChannelInterface
{
    /**
     * @return $this
     */
    public function withChannel(Proxy|ChannelInterface|string $channel): self;
}
