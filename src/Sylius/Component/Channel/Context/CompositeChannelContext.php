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
use Zend\Stdlib\PriorityQueue;

final class CompositeChannelContext implements ChannelContextInterface
{
    /**
     * @var PriorityQueue|ChannelContextInterface[]
     */
    private $channelContexts;

    public function __construct()
    {
        $this->channelContexts = new PriorityQueue();
    }

    /**
     * @param ChannelContextInterface $channelContext
     * @param int $priority
     */
    public function addContext(ChannelContextInterface $channelContext, int $priority = 0): void
    {
        $this->channelContexts->insert($channelContext, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel(): ChannelInterface
    {
        foreach ($this->channelContexts as $channelContext) {
            try {
                return $channelContext->getChannel();
            } catch (ChannelNotFoundException $exception) {
                continue;
            }
        }

        throw new ChannelNotFoundException();
    }
}
