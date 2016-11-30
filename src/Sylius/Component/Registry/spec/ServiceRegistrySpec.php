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
use spec\Sylius\Component\Registry\Fixture\SampleServiceInterface;
use Sylius\Component\Registry\ExistingServiceException;
use Sylius\Component\Registry\NonExistingServiceException;
use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ServiceRegistrySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(SampleServiceInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ServiceRegistry::class);
    }

    function it_implements_service_registry_interface()
    {
        $this->shouldImplement(ServiceRegistryInterface::class);
    }

    function it_initializes_services_array_by_default()
    {
        $this->all()->shouldReturn([]);
    }

    function it_registers_service_with_given_type(SampleServiceInterface $service)
    {
        $this->has('test')->shouldReturn(false);
        $this->register('test', $service);

        $this->has('test')->shouldReturn(true);
        $this->get('test')->shouldReturn($service);
    }

    function it_throws_exception_when_trying_to_register_service_with_taken_type(SampleServiceInterface $service)
    {
        $this->register('test', $service);

        $this
            ->shouldThrow(ExistingServiceException::class)
            ->duringRegister('test', $service)
        ;
    }

    function it_throws_exception_when_trying_to_register_service_without_required_interface(
        \stdClass $service
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringRegister('test', $service)
        ;
    }

    function it_unregisters_service_with_given_type(SampleServiceInterface $service)
    {
        $this->register('foo', $service);
        $this->has('foo')->shouldReturn(true);

        $this->unregister('foo');
        $this->has('foo')->shouldReturn(false);
    }

    function it_retrieves_registered_service_by_type(SampleServiceInterface $service)
    {
        $this->register('test', $service);
        $this->get('test')->shouldReturn($service);
    }

    function it_throws_exception_if_trying_to_get_service_of_non_existing_type()
    {
        $this
            ->shouldThrow(new NonExistingServiceException('service', 'foo', []))
            ->duringGet('foo')
        ;
    }
}
