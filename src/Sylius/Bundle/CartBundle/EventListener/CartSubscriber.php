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

use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Event\CartEvents;
use Sylius\Component\Cart\Event\CartItemEvent;
use Sylius\Component\Cart\Event\CartItemEvents;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ValidatorInterface;

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
     * @var ResourceManagerInterface
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
     * @param ResourceManagerInterface $cartManager
     * @param ValidatorInterface       $validator
     * @param CartProviderInterface    $cartProvider
     */
    public function __construct(ResourceManagerInterface $cartManager, ValidatorInterface $validator, CartProviderInterface $cartProvider)
    {
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
            CartItemEvents::PRE_ADD    => 'addItem',
            CartItemEvents::PRE_REMOVE => 'removeItem',
            CartEvents::PRE_CLEAR      => 'clearCart',
            CartEvents::PRE_SAVE       => 'saveCart',
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
     * @param CartEvent $event
     */
    public function clearCart(CartEvent $event)
    {
        $this->cartManager->remove($event->getCart());
        $this->cartManager->flush();

        $this->cartProvider->abandonCart();
    }

    /**
     * @param CartEvent $event
     */
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
