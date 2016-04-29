<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Purger;

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface;
use Sylius\Component\Cart\Purger\PurgerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Order\OrderTransitions;

/**
 * @author Ka-Yue Yeung <kayuey@gmail.com>
 */
class ExpiredOrdersPurger implements PurgerInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var OrderRepositoryInterface
     */
    protected $repository;

    /**
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param ObjectManager $manager
     * @param OrderRepositoryInterface $repository
     * @param FactoryInterface $factory
     */
    public function __construct(ObjectManager $manager, OrderRepositoryInterface $repository, FactoryInterface $factory)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->factory = $factory;
    }

    /**
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt(\DateTime $expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $orders = $this->repository->findExpired($this->expiresAt);
        foreach ($orders as $order) {
            // Check if order has any on-hold inventory units.
            $hasOnHoldInventoryUnits = $order->getItemUnits()->exists(function ($key, InventoryUnitInterface $inventoryUnit) {
                return InventoryUnitInterface::STATE_ONHOLD === $inventoryUnit->getInventoryState();
            });

            if (!$hasOnHoldInventoryUnits) {
                $this->purgeOrder($order);
            }
        }

        $this->manager->flush();
    }

    /**
     * @param OrderInterface $order
     */
    protected function purgeOrder(OrderInterface $order)
    {
        $this->factory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_ABANDON);
        $this->manager->persist($order);
    }
}
