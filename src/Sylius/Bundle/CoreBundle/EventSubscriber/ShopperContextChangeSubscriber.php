<?php

namespace Sylius\Bundle\CoreBundle\EventSubscriber;

use Sylius\Bundle\CoreBundle\SyliusCoreEvents;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ShopperContextChangeSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var CartProviderInterface
     */
    private $cartProvider;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        CartProviderInterface $cartProvider
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->cartProvider = $cartProvider;
    }

    public static function getSubscribedEvents()
    {
        return array(
            SyliusCoreEvents::SHOPPER_CONTEXT_CHANGE => 'onShopperContextChange',
            SyliusCoreEvents::PRE_CART_CHANGE => 'onPreCartChange',
            SyliusCoreEvents::CART_CHANGE => 'onCartChange',
            SyliusCoreEvents::POST_CART_CHANGE => 'onPostCartChange',
        );
    }

    /**
     * @TODO Add isPropagationStopped checkers
     */
    public function onShopperContextChange()
    {
        $cart = $this->cartProvider->getCart();
        $event = new GenericEvent($cart);

        $this->eventDispatcher->dispatch(SyliusCoreEvents::PRE_CART_CHANGE, $event);

        $this->eventDispatcher->dispatch(SyliusCoreEvents::CART_CHANGE);

        $this->eventDispatcher->dispatch(SyliusCoreEvents::POST_CART_CHANGE);
    }

    public function onPreCartChange(GenericEvent $event)
    {
    }

    public function onCartChange()
    {
    }

    public function onPostCartChange()
    {
    }
}