<?php

namespace Sylius\Bundle\CoreBundle\Purger;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Repository\OrderRepository;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Sylius\Bundle\CartBundle\Purger\PurgerInterface;

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
     * Pending order TTL.
     *
     * @var string
     */
    protected $pendingOrderTtl;

    public function __construct(ObjectManager $manager, OrderRepository $repository, $pendingOrderTtl)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->pendingOrderTtl = $pendingOrderTtl;
    }

    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        $expiresAt = new \DateTime();
        $expiresAt->sub(new \DateInterval($this->pendingOrderTtl));

        $orders = $this->repository->findExpiredPendingOrders($expiresAt);

        foreach ($orders as $order) {
            $this->purgeOrder($order);
        }

        $this->manager->flush();
    }

    /**
     * Purge a cart
     *
     * @param OrderInterface $cart
     */
    protected function purgeOrder(OrderInterface $cart)
    {
        $this->manager->remove($cart);
    }
}
