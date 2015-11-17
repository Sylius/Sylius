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

use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\Event;
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
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(
        ChannelContextInterface $channelContext,
        CartProviderInterface $cartProvider
    )
    {
        $this->channelContext = $channelContext;
        $this->cartProvider = $cartProvider;
    }

    /**
     * @param Event $event
     */
    public function processOrderChannel(Event $event)
    {
        $order = $this->cartProvider->getCart();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        $order->setChannel($this->channelContext->getChannel());
    }
}
