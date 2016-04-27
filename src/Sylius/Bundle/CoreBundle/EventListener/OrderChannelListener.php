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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sets currently selected channel on order object.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderChannelListener
{
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    /**
     * @param GenericEvent $event
     */
    public function processOrderChannel(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                OrderInterface::class
            );
        }

        if (null === $order->getChannel()) {
            $order->setChannel($this->channelContext->getChannel());
        }
    }
}
