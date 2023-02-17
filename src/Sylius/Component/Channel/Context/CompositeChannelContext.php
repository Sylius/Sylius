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

namespace Sylius\Component\Channel\Context;

use Sylius\Component\Channel\Model\ChannelInterface;

final class CompositeChannelContext implements ChannelContextInterface
{
    private iterable $channelContexts;

    public function __construct(iterable $channelContexts = [])
    {
        $this->channelContexts = $channelContexts instanceof \Traversable ? iterator_to_array($channelContexts) : $channelContexts;
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
