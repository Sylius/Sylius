<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Command;

/** @experimental */
interface ChannelCodeAwareInterface extends CommandAwareDataTransformerInterface
{
    public function getChannelCode(): ?string;

    public function setChannelCode(?string $channelCode): void;
}
