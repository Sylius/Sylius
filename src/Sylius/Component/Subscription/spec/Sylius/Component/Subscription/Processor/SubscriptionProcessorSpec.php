<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Component\Subscription\Processor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Subscription\Event\SubscriptionEvents;
use Sylius\Component\Subscription\Model\SubscriptionInterface;
use Sylius\Component\Subscription\Repository\SubscriptionRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SubscriptionProcessorSpec extends ObjectBehavior
{
    public function let(
        SubscriptionRepositoryInterface $repository,
        ObjectManager $manager,
        EventDispatcher $dispatcher
    ) {
        $this->beConstructedWith($repository, $manager, $dispatcher);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Subscription\Processor\SubscriptionProcessor');
    }

    public function it_implements_Sylius_recurring_scheduler_interface()
    {
        $this->shouldImplement('Sylius\Component\Subscription\Processor\SubscriptionProcessorInterface');
    }

    public function it_should_dispatch_events(
        SubscriptionRepositoryInterface $repository,
        EventDispatcher $dispatcher,
        SubscriptionInterface $subscription
    ) {
        $repository->findScheduled()->willReturn(array(
            $subscription
        ));

        $dispatcher
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_BATCH_START, Argument::type('Symfony\Component\EventDispatcher\GenericEvent'))
            ->shouldBeCalled();
        $dispatcher
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_BATCH_END, Argument::type('Symfony\Component\EventDispatcher\GenericEvent'))
            ->shouldBeCalled();

        $dispatcher
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_INITIALIZE, Argument::type('Sylius\Component\Subscription\Event\SubscriptionEvent'))
            ->shouldBeCalled();
        $dispatcher
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_SUCCESS, Argument::type('Sylius\Component\Subscription\Event\SubscriptionEvent'))
            ->shouldBeCalled();
        $dispatcher
            ->dispatch(SubscriptionEvents::SUBSCRIPTION_PROCESS_COMPLETED, Argument::type('Sylius\Component\Subscription\Event\SubscriptionEvent'))
            ->shouldBeCalled();

        $this->process();
    }

    public function it_should_set_processed_date(
        SubscriptionRepositoryInterface $repository,
        SubscriptionInterface $subscription
    ) {
        $repository->findScheduled()->willReturn(array(
            $subscription
        ));

        $subscription->setProcessedDate(Argument::any())->shouldBeCalled();

        $this->process();
    }

    public function it_should_persist_entity(
        SubscriptionRepositoryInterface $repository,
        ObjectManager $manager,
        SubscriptionInterface $subscription
    ) {
        $repository->findScheduled()->willReturn(array(
            $subscription
        ));

        $manager->flush()->shouldBeCalled();

        $this->process();
    }
}
