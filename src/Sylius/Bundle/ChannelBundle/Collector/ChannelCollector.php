<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\Collector;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelCollector extends DataCollector
{
    /**
     * @param ChannelRepositoryInterface $channelRepository
     * @param ChannelContextInterface $channelContext
     * @param bool $channelChangeSupport
     */
    public function __construct(ChannelRepositoryInterface $channelRepository, ChannelContextInterface $channelContext, $channelChangeSupport = false)
    {
        $this->data['channels'] = $channelRepository->findAll();

        try {
            $this->data['current_channel'] = $channelContext->getChannel();
        } catch (ChannelNotFoundException $exception) {
            $this->data['current_channel'] = null;
        }

        $this->data['channel_change_support'] = $channelChangeSupport;
    }

    /**
     * @return ChannelInterface[]
     */
    public function getChannels()
    {
        return $this->data['channels'];
    }

    /**
     * @return ChannelInterface
     */
    public function getCurrentChannel()
    {
        return $this->data['current_channel'];
    }

    /**
     * @return bool
     */
    public function isChannelChangeSupported()
    {
        return $this->data['channel_change_support'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius.collector.channel';
    }
}
