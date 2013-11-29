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
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\Storage\CartStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

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
     * Cart.
     *
     * @var CartInterface
     */
    protected $cart;

    /**
     * Constructor.
     *
     * @param CartStorageInterface $storage
     * @param ObjectManager        $manager
     * @param RepositoryInterface  $repository
     */
    public function __construct(CartStorageInterface $storage, ObjectManager $manager, RepositoryInterface $repository)
    {
        $this->storage = $storage;
        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCart()
    {
        return null !== $this->cart;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        if (null !== $this->cart) {
            return $this->cart;
        }

        $cartIdentifier = $this->storage->getCurrentCartIdentifier();

        if ($cartIdentifier && $cart = $this->getCartByIdentifier($cartIdentifier)) {
            return $this->cart = $cart;
        }

        return $this->cart = $this->repository->createNew();
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
}
