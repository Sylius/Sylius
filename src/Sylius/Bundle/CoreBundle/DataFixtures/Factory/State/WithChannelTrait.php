<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Sylius\Component\Core\Model\ChannelInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @mixin ModelFactory
 */
trait WithChannelTrait
{
    public function withChannel(Proxy|ChannelInterface|string $channel): self
    {
        return $this->addState(['channel' => $channel]);
    }
}
