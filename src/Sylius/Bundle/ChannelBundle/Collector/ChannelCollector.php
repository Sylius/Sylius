<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ChannelBundle\Collector;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class ChannelCollector extends DataCollector
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private ChannelContextInterface $channelContext,
        private bool $channelChangeSupport = false,
    ) {
    }

    public function getChannel(): ?array
    {
        return $this->data['channel'];
    }

    /**
     * @return iterable|ChannelInterface[]
     */
    public function getChannels(): iterable
    {
        return $this->data['channels'];
    }

    public function isChannelChangeSupported(): bool
    {
        return $this->data['channel_change_support'];
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        try {
            $channel = $this->pluckChannel($this->channelContext->getChannel());
        } catch (ChannelNotFoundException) {
            $channel = null;
        }

        $this->data = [
            'channel' => $channel,
            'channels' => $this->channelRepository->findAllWithBasicData(),
            'channel_change_support' => $this->channelChangeSupport,
        ];
    }

    public function reset(): void
    {
        $this->data['channel'] = null;
    }

    public function getName(): string
    {
        return 'sylius.channel_collector';
    }

    private function pluckChannel(ChannelInterface $channel): array
    {
        return [
            'name' => $channel->getName(),
            'hostname' => $channel->getHostname(),
            'code' => $channel->getCode(),
        ];
    }
}
