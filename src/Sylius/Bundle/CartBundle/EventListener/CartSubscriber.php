<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\EventListener;

use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Cart\Event\CartItemEvent;
use Sylius\Component\Cart\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartSubscriber implements EventSubscriberInterface
{
    /**
     * @var OrderItemQuantityModifierInterface
     */
    protected $orderItemQuantityModifier;

    /**
     * @param OrderItemQuantityModifierInterface $orderItemQuantityModifier
     */
    public function __construct(OrderItemQuantityModifierInterface $orderItemQuantityModifier)
    {
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SyliusCartEvents::ITEM_ADD_INITIALIZE => 'addItem',
        ];
    }

    /**
     * @param CartItemEvent $event
     */
    public function addItem(CartItemEvent $event)
    {
        $cart = $event->getCart();

        $item = $event->getItem();
        foreach ($cart->getItems() as $existingItem) {
            if ($item->equals($existingItem)) {
                $this->orderItemQuantityModifier->modify($existingItem, $existingItem->getQuantity() + $item->getQuantity());

                return;
            }
        }

        $cart->addItem($item);
    }
}
