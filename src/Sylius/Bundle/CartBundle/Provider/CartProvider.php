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
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\Storage\CartStorageInterface;
use Sylius\Bundle\CartBundle\SyliusCartEvents;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default provider cart.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartProvider implements CartProviderInterface
{
    /**
     * Cart identifier storage.
     *
     * @var CartStorageInterface
     */
    protected $storage;

    /**
     * Cart manager.
     *
     * @var ObjectManager
     */
    protected $manager;

    /**
     * Cart repository.
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Cart.
     *
     * @var CartInterface
     */
    protected $cart;

    /**
     * Constructor.
     *
     * @param CartStorageInterface      $storage
     * @param ObjectManager             $manager
     * @param RepositoryInterface       $repository
     * @param EventDispatcherInterface  $eventDispatcher
     */
    public function __construct(CartStorageInterface $storage, ObjectManager $manager, RepositoryInterface $repository, EventDispatcherInterface $eventDispatcher)
    {
        $this->storage = $storage;
        $this->manager = $manager;
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCart()
    {
        $this->initializeCart();

        return null !== $this->cart;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        $this->initializeCart();

        if (null !== $this->cart) {
            return $this->cart;
        }

        $this->cart = $this->repository->createNew();
        $this->eventDispatcher->dispatch(SyliusCartEvents::CART_INITIALIZE, new CartEvent($this->cart));

        return $this->cart;
    }

    /**
     * {@inheritdoc}
     */
    public function setCart(CartInterface $cart)
    {
        $this->cart = $cart;
        $this->storage->setCurrentCartIdentifier($cart);
    }

    /**
     * {@inheritdoc}
     */
    public function abandonCart()
    {
        if (null !== $this->cart) {
            $this->eventDispatcher->dispatch(SyliusCartEvents::CART_ABANDON, new CartEvent($this->cart));
        }

        $this->cart = null;
        $this->storage->resetCurrentCartIdentifier();
    }

    /**
     * Gets cart by cart identifier.
     *
     * @param mixed $identifier
     *
     * @return CartInterface|null
     */
    protected function getCartByIdentifier($identifier)
    {
        return $this->repository->find($identifier);
    }

    /**
     * Tries to initialize cart if there is data in storage.
     */
    private function initializeCart()
    {
        if (null === $this->cart) {
            $cartIdentifier = $this->storage->getCurrentCartIdentifier();
            if ($cartIdentifier) {
                $this->cart = $this->getCartByIdentifier($cartIdentifier);
            }
        }
    }
}
