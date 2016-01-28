<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel\Context;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Channel\ChannelResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides the context of currently used channel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
interface ChannelContextInterface
{
    /**
     * @return ChannelInterface
     */
    public function getChannel();

    /**
     * @param ChannelInterface $channel
     */
    public function setChannel(ChannelInterface $channel);

    /**
     * @return string
     */
    public function getLatestHostname();

    /**
     * @param string $hostname
     */
    public function setLatestHostname($hostname);

    /**
     * @return ChannelResolverInterface
     */
    public function getChannelResolver();

    /**
     * @return RequestStack
     */
    public function getRequestStack();
}
