<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

use Zenstruck\Foundry\ModelFactory;

/**
 * @mixin ModelFactory
 */
trait WithChannelsTrait
{
    /**
     * @return $this
     */
    public function withChannels(array $channels): self
    {
        return $this->addState(['channels' => $channels]);
    }
}
