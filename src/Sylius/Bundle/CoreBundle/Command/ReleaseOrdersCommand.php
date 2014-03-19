<?php

/*
 * This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sylius\Bundle\CoreBundle\SyliusOrderEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Repository\OrderRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Command to release expired pending orders
 *
 * @author Foo Pang <foo.pang@gmail.com>
 */
class ReleaseOrdersCommand extends ContainerAwareCommand
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

    /**
     * Inventory holding duration.
     *
     * @var string
     */
    protected $holdingDuration;

    protected function configure()
    {
        $this
            ->setName('sylius:order:release')
            ->setDescription('Release expired pending orders')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->manager = $this->getContainer()->get('sylius.manager.order');
        $this->repository = $this->getContainer()->get('sylius.repository.order');
        $this->dispatcher = $this->getContainer()->get('event_dispatcher');
        $this->holdingDuration = $this->getContainer()->getParameter('sylius.inventory.holding.duration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Release expired pending orders...');

        $expiresAt = new \DateTime(sprintf('-%s', $this->holdingDuration));

        $orders = $this->repository->findExpiredPendingOrders($expiresAt);
        foreach ($orders as $order) {
            $this->dispatcher->dispatch(SyliusOrderEvents::PRE_RELEASE, new GenericEvent($order));
            $this->dispatcher->dispatch(SyliusOrderEvents::POST_RELEASE, new GenericEvent($order));
            $this->manager->persist($order);
        }
        $this->manager->flush();

        $output->writeln('Expired pending orders released.');
    }
}
