<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Subscription\Processor;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Subscription\Event\SubscriptionEvent;
use Sylius\Component\Subscription\Event\SubscriptionEvents;
use Sylius\Component\Subscription\Model\SubscriptionInterface;
use Sylius\Component\Subscription\Repository\SubscriptionRepositoryInterface;
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
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param SubscriptionRepositoryInterface $repository
     * @param ObjectManager $manager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        SubscriptionRepositoryInterface $repository,
        ObjectManager $manager,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        $subscriptions = $this->repository->findScheduled();

        $this->dispatcher->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_BATCH_START, new GenericEvent($subscriptions));

        foreach ($subscriptions as $subscription) {

            try {
                $this->dispatcher->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_INITIALIZE, new SubscriptionEvent($subscription));
                $this->processSubscription($subscription);
            } catch (\Exception $e) {
                $this->dispatcher->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_ERROR, new SubscriptionEvent($subscription, array('exception' => $e)));
                continue;
            }

            $this->dispatcher->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_SUCCESS, new SubscriptionEvent($subscription));

            $this->manager->flush();
            $this->dispatcher->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_COMPLETED, new SubscriptionEvent($subscription));
        }

        $this->dispatcher->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_BATCH_END, new GenericEvent($subscriptions));
    }

    protected function processSubscription(SubscriptionInterface $subscription)
    {
        $subscription
            ->setProcessedDate(new \DateTime())
        ;
    }
}
