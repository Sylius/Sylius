<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Zenstruck\Foundry\Proxy;

interface WithChannelInterface
{
    public function withChannel(Proxy|ChannelInterface|string $channel): static;
}
