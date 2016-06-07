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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Event\CartItemEvent;
use Sylius\Component\Cart\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    protected $cartManager;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    protected $orderItemQuantityModifier;

    /**
     * @param ObjectManager $cartManager
     * @param ValidatorInterface $validator
     * @param OrderItemQuantityModifierInterface $orderItemQuantityModifier
     */
    public function __construct(
        ObjectManager $cartManager,
        ValidatorInterface $validator,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->cartManager = $cartManager;
        $this->validator = $validator;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SyliusCartEvents::ITEM_ADD_INITIALIZE => 'addItem',
            SyliusCartEvents::ITEM_REMOVE_INITIALIZE => 'removeItem',
            SyliusCartEvents::CART_CLEAR_INITIALIZE => 'clearCart',
            SyliusCartEvents::CART_SAVE_INITIALIZE => 'saveCart',
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

    /**
     * @param CartItemEvent $event
     */
    public function removeItem(CartItemEvent $event)
    {
        $cart = $event->getCart();
        $cart->removeItem($event->getItem());
    }

    /**
     * @param CartEvent $event
     */
    public function clearCart(CartEvent $event)
    {
        $this->cartManager->remove($event->getCart());
        $this->cartManager->flush();
    }

    /**
     * @param CartEvent $event
     */
    public function saveCart(CartEvent $event)
    {
        $cart = $event->getCart();

        $errors = $this->validator->validate($cart);
        $valid = 0 === count($errors);

        if ($valid) {
            $this->cartManager->persist($cart);
            $this->cartManager->flush();
        }
    }
}
