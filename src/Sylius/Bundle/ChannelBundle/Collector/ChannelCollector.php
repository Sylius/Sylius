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
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param ChannelContextInterface $channelContext
     * @param bool $channelChangeSupport
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ChannelContextInterface $channelContext,
        bool $channelChangeSupport = false
    ) {
        $this->channelContext = $channelContext;

        $this->data = [
            'channel' => null,
            'channels' => $channelRepository->findAll(),
            'channel_change_support' => $channelChangeSupport,
        ];
    }

    /**
     * @return ChannelInterface|null
     */
    public function getChannel(): ?ChannelInterface
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

    /**
     * @return bool
     */
    public function isChannelChangeSupported(): bool
    {
        return $this->data['channel_change_support'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
        try {
            $this->data['channel'] = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius.channel_collector';
    }
}
