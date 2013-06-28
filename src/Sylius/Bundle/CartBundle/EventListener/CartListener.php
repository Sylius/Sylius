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
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\CartBundle\Event\CartEvent;
use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ValidatorInterface;

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
     * Validator.
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    private $cartProvider;

    /**
     * Constructor.
     *
     * @param ObjectManager      $cartManager
     * @param ValidatorInterface $validator
     * @param CartPRovider       $cartProvider
     */
    public function __construct(ObjectManager $cartManager, ValidatorInterface $validator, CartProviderInterface $cartProvider)
    {
        $this->cartManager  = $cartManager;
        $this->validator    = $validator;
        $this->cartProvider = $cartProvider;
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

        if ($event->isFresh()) {
            $this->refreshCart($cart);
        }

        if ($valid) {
            $this->cartManager->persist($cart);
            $this->cartManager->flush();
            $this->cartProvider->setCart($cart);
        }
    }

    /**
     * @param CartInterface $cart
     */
    private function refreshCart(CartInterface $cart)
    {
        $cart->calculateTotal();
        $cart->setTotalItems($cart->countItems());
        
        // Set Total Quantity
        $totalQuantity = 0;
        foreach ($cart->getItems() as $item) {
            $totalQuantity += $item->getQuantity();
            $this->cartManager->persist($item);
        }
        $cart->setTotalQuantity($totalQuantity);
    }
}
