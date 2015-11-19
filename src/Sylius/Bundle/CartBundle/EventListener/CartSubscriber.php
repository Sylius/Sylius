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
use Sylius\Bundle\CoreBundle\SyliusCoreEvents;
use Sylius\Component\Cart\Event\CartItemEvent;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\SyliusCartEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Cart & item changes listener.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartSubscriber implements EventSubscriberInterface
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
    public function __construct(
        ObjectManager $cartManager,
        ValidatorInterface $validator,
        CartProviderInterface $cartProvider
    ) {
        $this->cartManager  = $cartManager;
        $this->validator    = $validator;
        $this->cartProvider = $cartProvider;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            SyliusCartEvents::ITEM_ADD_INITIALIZE    => 'addItem',
            SyliusCartEvents::ITEM_REMOVE_INITIALIZE => 'removeItem',
            SyliusCartEvents::CART_CLEAR_INITIALIZE  => 'clearCart',
            SyliusCoreEvents::POST_CART_CHANGE       => ['saveCart', -254]
        );
    }

    /**
     * @param CartItemEvent $event
     */
    public function addItem(CartItemEvent $event)
    {
        $cart = $event->getCart();
        $cart->addItem($event->getItem());
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
     * @param Event $event
     */
    public function clearCart(Event $event)
    {
        $cart = $this->cartProvider->getCart();

        $this->cartManager->remove($cart);
        $this->cartManager->flush();
        $this->cartProvider->abandonCart();
    }

    /**
     * @param Event $event
     */
    public function saveCart(Event $event)
    {
        $cart  = $this->cartProvider->getCart();

        $errors = $this->validator->validate($cart);
        $valid  = 0 === count($errors);

        if ($valid) {
            $this->cartManager->persist($cart);
            $this->cartManager->flush();

            $this->cartProvider->setCart($cart);
        }
    }
}
