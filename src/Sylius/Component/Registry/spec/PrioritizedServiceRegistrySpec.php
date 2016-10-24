<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Registry;

require_once __DIR__.'/Fixture/SampleServiceInterface.php';

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Sylius\Component\Registry\Fixture\SampleServiceInterface;
use Sylius\Component\Registry\NonExistingServiceException;
use Sylius\Component\Registry\PrioritizedServiceRegistry;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;
use Zend\Stdlib\PriorityQueue;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class PrioritizedServiceRegistrySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(SampleServiceInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PrioritizedServiceRegistry::class);
    }

    function it_implements_prioritized_service_registry_interface()
    {
        $this->shouldImplement(PrioritizedServiceRegistryInterface::class);
    }

    function it_initializes_services_priority_queue_by_default()
    {
        $this->all()->shouldReturnAnInstanceOf(PriorityQueue::class);

        $this->all()->shouldBeEmpty();
    }

    function it_registers_services_in_the_correct_prioritized_order(
        SampleServiceInterface $serviceOne,
        SampleServiceInterface $serviceTwo,
        SampleServiceInterface $serviceThree
    ) {
        $this->has($serviceOne)->shouldReturn(false);
        $this->has($serviceTwo)->shouldReturn(false);
        $this->has($serviceThree)->shouldReturn(false);

        $this->register($serviceOne);
        $this->register($serviceTwo, -1);
        $this->register($serviceThree, 1);

        $this->has($serviceOne)->shouldReturn(true);
        $this->has($serviceTwo)->shouldReturn(true);
        $this->has($serviceThree)->shouldReturn(true);

        $this->all()->shouldHaveCount(3);
        $this->all()->shouldHavePriority(1);
        $this->all()->shouldHavePriority(0);
        $this->all()->shouldHavePriority(-1);
    }

    function it_throws_exception_when_trying_to_register_service_without_required_interface(\stdClass $service)
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringRegister($service)
        ;
    }

    function it_throws_exception_when_trying_to_check_for_a_registered_service_without_required_interface(
        \stdClass $service
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringHas($service)
        ;
    }

    function it_unregisters_service(SampleServiceInterface $service)
    {
        $this->register($service);
        $this->has($service)->shouldReturn(true);

        $this->unregister($service);
        $this->has($service)->shouldReturn(false);
    }

    function it_throws_exception_if_trying_to_unregister_service_of_non_existing_type(SampleServiceInterface $service)
    {
        $this
            ->shouldThrow(NonExistingServiceException::class)
            ->duringUnregister($service)
        ;
    }
}
