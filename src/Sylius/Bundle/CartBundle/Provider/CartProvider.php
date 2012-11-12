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

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Storage\CartStorageInterface;
use Sylius\Bundle\ResourceBundle\Manager\ResourceManagerInterface;
use Sylius\Bundle\ResourceBundle\Repository\ResourceRepositoryInterface;

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
     * @var ResourceManagerInterface
     */
    protected $manager;

    /**
     * Cart repository.
     *
     * @var ResourceRepositoryInterface
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
     * @param CartStorageInterface        $storage
     * @param ResourceManagerInterface    $manager
     * @param ResourceRepositoryInterface $repository
     */
    public function __construct(CartStorageInterface $storage, ResourceManagerInterface $manager, ResourceRepositoryInterface $repository)
    {
        $this->storage = $storage;
        $this->manager = $manager;
        $this->repository = $repository;
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

        $cart = $this->manager->create();
        $this->manager->persist($cart);

        $this->setCart($cart);

        return $cart;
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
     * @param  mixed              $identifier
     * @return CartInterface|null
     */
    protected function getCartByIdentifier($identifier)
    {
        return $this->repository->find($identifier);
    }
}
