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
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Event\CartItemEvent;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\SyliusCartEvents;
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
    protected $cartManager;

    /**
     * Validator.
     *
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Cart provider.
     *
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * Constructor.
     *
     * @param ObjectManager         $cartManager
     * @param ValidatorInterface    $validator
     * @param CartProviderInterface $cartProvider
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

    public function addItem(CartItemEvent $event)
    {
        $cart = $event->getCart();
        $cart->addItem($event->getItem());
    }

    public function removeItem(CartItemEvent $event)
    {
        $cart = $event->getCart();
        $cart->removeItem($event->getItem());
    }

    public function clearCart(CartEvent $event)
    {
        $this->cartManager->remove($event->getCart());
        $this->cartManager->flush();
        $this->cartProvider->abandonCart();
    }

    public function saveCart(CartEvent $event)
    {
        $cart  = $event->getCart();

        $errors = $this->validator->validate($cart);
        $valid  = 0 === count($errors);

        if ($valid) {
            $this->cartManager->persist($cart);
            $this->cartManager->flush();

            $this->cartProvider->setCart($cart);
        }
    }
}
