<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

interface WithChannelInterface
{
    public function withChannels(array $channels): self;
}
