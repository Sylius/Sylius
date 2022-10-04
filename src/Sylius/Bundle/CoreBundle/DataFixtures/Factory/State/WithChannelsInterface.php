<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\State;

interface WithChannelsInterface
{
    public function withChannels(array $channels): self;
}
