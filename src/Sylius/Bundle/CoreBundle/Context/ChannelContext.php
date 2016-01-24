<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Context;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Channel\ChannelContextInterface;
use Sylius\Component\Core\Channel\ChannelResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Core channel context, which is aware of multiple channels.
 *
 * @author Kristian Løvstrøm <kristian@loevstroem.dk>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelContext implements ChannelContextInterface
{
    /**
     * @var ChannelInterface
     */
    private $channel;

    /**
     * @var string
     */
    private $latestHostname = null;

    /**
     * @var bool
     */
    private $isFresh = false;

    /**
     * @var ChannelResolverInterface
     */
    private $channelResolver;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param ChannelResolverInterface $channelResolver
     * @param RequestStack $requestStack
     */
    public function __construct(ChannelResolverInterface $channelResolver, RequestStack $requestStack)
    {
        $this->channelResolver = $channelResolver;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        $this->scheduleRefreshIfHostnameHaveChanged($this->requestStack->getMasterRequest());

        if (false === $this->isFresh) {
            $this->channel = $this->channelResolver->resolve($this->latestHostname);
            $this->isFresh = true;
        }

        return $this->channel;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannel(ChannelInterface $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @param Request $request
     */
    private function scheduleRefreshIfHostnameHaveChanged(Request $request = null)
    {
        if (null === $request) {
            return;
        }

        $hostname = $request->getHost();
        if ($this->latestHostname === $hostname) {
            return;
        }

        $this->latestHostname = $hostname;
        $this->isFresh = false;
    }
}
