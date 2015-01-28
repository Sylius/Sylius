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
use Symfony\Component\DependencyInjection\ContainerInterface;


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
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ChannelResolverInterface $channelResolver
     * @param ContainerInterface       $container
     */
    public function __construct(ChannelResolverInterface $channelResolver, ContainerInterface $container)
    {
        $this->channelResolver = $channelResolver;
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function getChannel()
    {
        if (!$this->channel && $this->container->isScopeActive('request')) {
            $this->channel = $this->channelResolver->resolve($this->container->get('request'));
        }

        return parent::getChannel();
    }
}
