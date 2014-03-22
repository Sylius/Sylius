<?php

/*
 * This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Releaser;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Repository\OrderRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\SyliusOrderEvents;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Release expired orders.
 *
 * @author Foo Pang <foo.pang@gmail.com>
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
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(ObjectManager $manager, OrderRepository $repository, EventDispatcherInterface $dispatcher)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function release(\DateTime $expiresAt)
    {
        $orders = $this->repository->findExpired($expiresAt);

        foreach ($orders as $order) {
            $this->dispatcher->dispatch(SyliusOrderEvents::PRE_RELEASE, new GenericEvent($order));
            $this->manager->persist($order);
        }
        $this->manager->flush();

        foreach ($orders as $order) {
            $this->dispatcher->dispatch(SyliusOrderEvents::POST_RELEASE, new GenericEvent($order));
        }
    }
}
