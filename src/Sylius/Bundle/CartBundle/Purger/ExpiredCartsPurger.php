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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Repository\CartRepositoryInterface;

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
     * @var ObjectManager
     */
    protected $manager;

    /**
     * Cart repository.
     *
     * @var RepositoryInterface
     */
    protected $repository;

    public function __construct(ObjectManager $manager, CartRepositoryInterface $repository)
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

        $this->manager->flush();
    }

    /**
     * Purge a cart
     *
     * @param CartInterface $cart
     */
    protected function purgeCart(CartInterface $cart)
    {
        $this->manager->remove($cart);
    }
}
