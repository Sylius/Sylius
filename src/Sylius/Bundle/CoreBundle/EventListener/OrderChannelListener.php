<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sets currently selected channel on order object.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderChannelListener
{
    const EXCEPTION_MESSAGE_PATTERN = 'Expected value of type: %s, %s given';

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(
        ChannelContextInterface $channelContext
    )
    {
        $this->channelContext = $channelContext;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \UnexpectedValueException if event doesn't contain order
     */
    public function processOrderChannel(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \UnexpectedValueException(
                sprintf(
                    self::EXCEPTION_MESSAGE_PATTERN,
                    OrderInterface::class,
                    is_object($order) ? get_class($order) : gettype($order)
                )
            );
        }

        $order->setChannel($this->channelContext->getChannel());
    }
}
