<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Purger;

use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Purger\PurgerInterface;
use Sylius\Component\Cart\Repository\CartRepositoryInterface;
use Sylius\Component\Resource\Manager\DomainManagerInterface;

/**
 * Default cart purger.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ExpiredCartsPurger implements PurgerInterface
{
    /**
     * Cart manager.
     *
     * @var DomainManagerInterface
     */
    protected $manager;

    /**
     * Cart repository.
     *
     * @var CartRepositoryInterface
     */
    protected $repository;

    public function __construct(DomainManagerInterface $manager, CartRepositoryInterface $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $cartsToPurge = $this->repository->findExpiredCarts();

        foreach ($cartsToPurge as $cart) {
            $this->purgeCart($cart);
        }
    }

    /**
     * Purge a cart
     *
     * @param CartInterface $cart
     */
    protected function purgeCart(CartInterface $cart)
    {
        $this->manager->delete($cart);
    }
}
