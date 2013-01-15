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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ValidatorInterface;

use Sylius\Bundle\CartBundle\SyliusCartEvents;
use Sylius\Bundle\CartBundle\Event\CartEvent;
use Sylius\Bundle\CartBundle\Model\CartInterface;

/**
 * Cart & item changes listener.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartListener implements EventSubscriberInterface
{
    /**
     * Cart manager.
     *
     * @var ObjectManager
     */
    private $cartManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Constructor.
     *
     * @param ObjectManager      $cartManager
     * @param ValidatorInterface $validator
     */
    public function __construct(ObjectManager $cartManager, ValidatorInterface $validator)
    {
        $this->cartManager = $cartManager;
        $this->validator   = $validator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            SyliusCartEvents::ITEM_ADD_INITIALIZE    => 'addItem',
            SyliusCartEvents::ITEM_REMOVE_INITIALIZE => 'removeItem',
            SyliusCartEvents::CART_CLEAR_INITIALIZE  => 'clearCart',
            SyliusCartEvents::CART_SAVE_INITIALIZE   => 'saveCart',
        );
    }

    public function addItem(CartEvent $event)
    {
        $cart = $event->getCart();
        $cart->addItem($event->getItem());

        if ($event->isFresh()) {
            $this->refreshCart($cart);
        }
    }

    public function removeItem(CartEvent $event)
    {
        $cart = $event->getCart();
        $cart->removeItem($event->getItem());

        if ($event->isFresh()) {
            $this->refreshCart($cart);
        }
    }

    public function clearCart(CartEvent $event)
    {
        $this->cartManager->remove($event->getCart());
        $this->cartManager->flush();
    }

    public function saveCart(CartEvent $event)
    {
        $cart  = $event->getCart();
        $valid = true;

        if (!$event->isValid()) {
            $errors = $this->validator->validate($cart);
            $valid  = 0 === count($errors);
        }

        if ($valid) {
            $this->cartManager->persist($cart);
            $this->cartManager->flush();
        }
    }

    /**
     * @param CartInterface $cart
     */
    private function refreshCart(CartInterface $cart)
    {
        $cart->calculateTotal();
        $cart->setTotalItems($cart->countItems());
    }
}
