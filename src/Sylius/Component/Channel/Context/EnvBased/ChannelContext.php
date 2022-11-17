<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Channel\Context\EnvBased;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

final class ChannelContext implements ChannelContextInterface
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private ?string $channelCodeFromEnv,
    ) {
    }

    public function getChannel(): ChannelInterface
    {
        if ('cli' !== PHP_SAPI || null === $this->channelCodeFromEnv) {
            throw new ChannelNotFoundException();
        }

        $channel = $this->channelRepository->findOneByCode($this->channelCodeFromEnv);

        if (null === $channel) {
            throw new ChannelNotFoundException();
        }

        return $channel;
    }
}
