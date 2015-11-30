<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Provider;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Default cart provider.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartProvider implements CartProviderInterface
{
    /**
     * @var CartContextInterface
     */
    protected $cartContext;

    /**
     * @var FactoryInterface
     */
    protected $cartFactory;

    /**
     * @var RepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ObjectManager
     */
    protected $cartManager;

    /**
     * @param CartContextInterface     $cartContext
     * @param FactoryInterface         $cartFactory
     * @param RepositoryInterface      $cartRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @param ObjectManager            $cartManager
     */
    public function __construct(
        CartContextInterface $cartContext,
        FactoryInterface $cartFactory,
        RepositoryInterface $cartRepository,
        EventDispatcherInterface $eventDispatcher,
        ObjectManager $cartManager
    )
    {
        $this->cartContext = $cartContext;
        $this->cartFactory = $cartFactory;
        $this->cartRepository = $cartRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCart()
    {
        return (bool) $this->cartContext->getCurrentCartIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        $cart = $this->provideCart();

        return $cart;
    }

    /**
     * {@inheritdoc}
     */
    public function setCart(CartInterface $cart)
    {
        $this->cartContext->setCurrentCartIdentifier($cart);
    }

    /**
     * {@inheritdoc}
     */
    public function abandonCart()
    {
        $cart = $this->provideCart();

        $this->eventDispatcher->dispatch(
            SyliusCartEvents::CART_ABANDON,
            new GenericEvent($cart)
        );

        $this->cartContext->resetCurrentCartIdentifier();
    }

    /**
     * Tries to initialize cart if there is data in storage.
     * If not, returns new instance from resourceFactory
     *
     * @return CartInterface
     */
    private function provideCart()
    {
        $cartIdentifier = $this->cartContext->getCurrentCartIdentifier();
        if ($cartIdentifier !== null) {
            $cart = $this->cartRepository->find($cartIdentifier);

            if ($cart !== null ) {
                return $cart;
            }
        }

        $cart = $this->cartFactory->createNew();

        $event = new GenericEvent($cart);
        $this->eventDispatcher->dispatch(SyliusCartEvents::CART_INITIALIZE, $event);

        $cart = $event->getSubject();

        $this->cartManager->persist($cart);
        $this->cartManager->flush();

        $this->cartContext->setCurrentCartIdentifier($cart);

        return $cart;
    }
}
