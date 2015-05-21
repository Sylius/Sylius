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

use Sylius\Component\Channel\Context\ChannelContext as BaseChannelContext;
use Sylius\Component\Core\Channel\ChannelContextInterface;
use Sylius\Component\Core\Channel\ChannelResolverInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;


/**
 * Core channel context, which is aware of multiple channels.
 *
 * @author Kristian Løvstrøm <kristian@loevstroem.dk>
 */
class ChannelContext extends BaseChannelContext implements ChannelContextInterface
{
    /**
     * @var ChannelResolverInterface
     */
    protected $channelResolver;

    /**
     * @param ChannelResolverInterface $channelResolver
     */
    public function __construct(ChannelResolverInterface $channelResolver)
    {
        $this->channelResolver = $channelResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        if (null === $this->channel) {
            $this->channel = $this->channelResolver->resolve();
        }
        return $this->channel;
    }

    /**
     * @inheritdoc
     */
    public function onKernelRequest(KernelEvent $event)
    {
        if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {
            $this->channel = $this->channelResolver->resolve($event->getRequest()->getHost());
        }
    }
}
