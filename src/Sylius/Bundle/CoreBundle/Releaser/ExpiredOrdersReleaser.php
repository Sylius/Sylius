<?php

namespace Sylius\Bundle\CoreBundle\Releaser;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Repository\OrderRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\SyliusOrderEvents;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Release expired pending orders
 *
 * @author Ka-Yue Yeung <kayuey@gmail.com>
 */
class ExpiredOrdersReleaser implements ReleaserInterface
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
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Inventory holding duration.
     *
     * @var string
     */
    protected $holdingDuration;

    public function __construct(ObjectManager $manager, OrderRepository $repository, EventDispatcherInterface $dispatcher, $holdingDuration)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
        $this->holdingDuration = $holdingDuration;
    }

    /**
     * {@inheritdoc}
     */
    public function release()
    {
        $expiresAt = (new \DateTime)->modify(sprintf('-%s', $this->holdingDuration));

        $orders = $this->repository->findExpiredPendingOrders($expiresAt);
        foreach ($orders as $order) {
            $this->dispatcher->dispatch(SyliusOrderEvents::PRE_RELEASE, new GenericEvent($order));
            $this->dispatcher->dispatch(SyliusOrderEvents::POST_RELEASE, new GenericEvent($order));
            $this->manager->persist($order);
        }
        $this->manager->flush();
    }
}
