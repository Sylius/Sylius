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

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\OrderTransitions;

/**
 * @author Foo Pang <foo.pang@gmail.com>
 */
class ExpiredOrdersReleaser implements ReleaserInterface
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
     * {@inheritdoc}
     */
    public function release(\DateTime $expiresAt)
    {
        $orders = $this->repository->findExpired($expiresAt);

        foreach ($orders as $order) {
            $this->factory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_RELEASE, true);
        }

        $this->manager->flush();
    }
}
