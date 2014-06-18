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
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Cart\Purger\PurgerInterface;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

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
     * Expires at.
     *
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    public function __construct(ObjectManager $manager, OrderRepository $repository, FactoryInterface $factory)
    {
        $this->manager    = $manager;
        $this->repository = $repository;
        $this->factory    = $factory;
    }

    /**
     * Set expires at.
     *
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
     * Purge an order.
     *
     * @param OrderInterface $order
     */
    protected function purgeOrder(OrderInterface $order)
    {
        $this->factory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_ABANDON);
        $this->manager->persist($order);
    }
}
