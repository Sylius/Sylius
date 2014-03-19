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
use Sylius\Bundle\CoreBundle\Repository\OrderRepository;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CartBundle\Purger\PurgerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface;

/**
 * Purge expired pending orders
 *
 * @author Ka-Yue Yeung <kayuey@gmail.com>
 */
class ExpiredOrdersPurger implements PurgerInterface
{
    /**
     * Order manager.
     *
     * @var ObjectManager
     */
    protected $manager;

    /**
     * Order repository.
     *
     * @var OrderRepository
     */
    protected $repository;

    /**
     * Pending order duration.
     *
     * @var string
     */
    protected $pendingDuration;

    public function __construct(ObjectManager $manager, OrderRepository $repository, $pendingDuration)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->pendingDuration = $pendingDuration;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $expiresAt = new \DateTime(sprintf('-%s', $this->pendingDuration));

        $orders = $this->repository->findExpiredPendingOrders($expiresAt);
        foreach ($orders as $order) {
            // Check if order has any on-hold inventory units.
            $hasOnHoldInventoryUnits = $order->getInventoryUnits()->exists(function ($key, InventoryUnitInterface $inventoryUnit) {
                return InventoryUnitInterface::STATE_ONHOLD === $inventoryUnit->getInventoryState();
            });

            if (!$hasOnHoldInventoryUnits) {
                $this->purgeOrder($order);
            }
        }

        $this->manager->flush();
    }

    /**
     * Purge an order
     *
     * @param OrderInterface $order
     */
    protected function purgeOrder(OrderInterface $order)
    {
        $order->setState(OrderInterface::STATE_ABANDONED);
        $this->manager->persist($order);
    }
}
