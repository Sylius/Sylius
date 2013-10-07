<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\Processor;


use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\SubscriptionBundle\Event\SubscriptionEvents;
use Sylius\Bundle\SubscriptionBundle\Model\SubscriptionInterface;
use Sylius\Bundle\SubscriptionBundle\Repository\SubscriptionRepositoryInterface;
use Sylius\Bundle\SubscriptionBundle\Scheduler\SubscriptionSchedulerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Subscription Processor
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SubscriptionProcessor implements SubscriptionProcessorInterface
{
    /**
     * @var SubscriptionRepositoryInterface
     */
    protected $repository;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var SubscriptionSchedulerInterface
     */
    protected $scheduler;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param SubscriptionRepositoryInterface $repository
     * @param ObjectManager $manager
     * @param SubscriptionSchedulerInterface $scheduler
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        SubscriptionRepositoryInterface $repository,
        ObjectManager $manager,
        EventDispatcherInterface $dispatcher,
        SubscriptionSchedulerInterface $scheduler = null
    )
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->dispatcher = $dispatcher;
        $this->scheduler = $scheduler;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        $subscriptions = $this->repository->findScheduled();

        foreach ($subscriptions as $subscription) {

            try {
                $this->dispatcher->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_INITIALIZE, new GenericEvent($subscription));
                $this->processSubscription($subscription);
            } catch (\Exception $e) {
                $this->dispatcher->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_ERROR, $subscription, array('exception' => $e));
                continue;
            }

            $this->manager->flush();
            $this->dispatcher->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_COMPLETED, new GenericEvent($subscription));
        }
    }

    protected function processSubscription(SubscriptionInterface $subscription)
    {
        $subscription
            ->setProcessedDate(new \DateTime())
        ;

        if ($this->scheduler) {
            $this->scheduler->schedule($subscription);
        }
    }
}