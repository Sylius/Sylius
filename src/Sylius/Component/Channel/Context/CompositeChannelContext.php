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

namespace Sylius\Component\Channel\Context;

use Laminas\Stdlib\PriorityQueue;
use Sylius\Component\Channel\Model\ChannelInterface;

final class CompositeChannelContext implements ChannelContextInterface
{
    /** @var PriorityQueue<ChannelContextInterface> */
    private PriorityQueue $channelContexts;

    public function __construct()
    {
        $this->channelContexts = new PriorityQueue();
    }

    public function addContext(ChannelContextInterface $channelContext, int $priority = 0): void
    {
        $this->channelContexts->insert($channelContext, $priority);
    }

    public function getChannel(): ChannelInterface
    {
        foreach ($this->channelContexts as $channelContext) {
            try {
                return $channelContext->getChannel();
            } catch (ChannelNotFoundException) {
                continue;
            }
        }

        throw new ChannelNotFoundException();
    }
}
