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
use Finite\Factory\FactoryInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Order\OrderTransitions;

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
     * {@inheritdoc}
     */
    public function release(\DateTime $expiresAt)
    {
        $orders = $this->repository->findExpired($expiresAt);

        foreach ($orders as $order) {
            $stateMachine = $this->factory->get($order, OrderTransitions::GRAPH);
            if ($stateMachine->can(OrderTransitions::SYLIUS_RELEASE)) {
                $stateMachine->apply(OrderTransitions::SYLIUS_RELEASE);
            }
        }

        $this->manager->flush();
    }
}
