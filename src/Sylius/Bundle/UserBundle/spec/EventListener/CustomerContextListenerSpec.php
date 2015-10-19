<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\Context\CustomerContext;
use Sylius\Component\User\Model\CustomerAwareInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class CustomerContextListenerSpec extends ObjectBehavior
{
    function let(CustomerContext $customerContext)
    {
        $this->beConstructedWith($customerContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\CustomerContextListener');
    }

    function it_sets_subjects_customer_in_context(
        $customerContext,
        GenericEvent $event,
        CustomerAwareInterface $subject,
        CustomerInterface $customer
    ) {
        $event->getSubject()->willReturn($subject);
        $subject->getCustomer()->willReturn($customer);
        $customerContext->setCustomer($customer)->shouldBeCalled();

        $this->setCustomerContextFromSubject($event);
    }

    function it_throws_exception_if_event_subject_is_not_a_customer_aware(GenericEvent $event)
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this
            ->shouldThrow('Sylius\Component\Resource\Exception\UnexpectedTypeException')
            ->duringSetCustomerContextFromSubject($event)
        ;
    }
}
