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

use PhpSpec\ObjectBehavior;
use Symfony\Component\Routing\RouteCompilerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ServiceRegistrySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('Symfony\Component\Routing\RouterInterface');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Registry\ServiceRegistry');
    }

    public function it_implements_service_registry_interface()
    {
        $this->shouldImplement('Sylius\Component\Registry\ServiceRegistryInterface');
    }

    public function it_initializes_services_array_by_default()
    {
        $this->all()->shouldReturn(array());
    }

    public function it_registers_service_with_given_type(RouterInterface $router)
    {
        $this->has('test')->shouldReturn(false);
        $this->register('test', $router);

        $this->has('test')->shouldReturn(true);
        $this->get('test')->shouldReturn($router);
    }

    public function it_throws_exception_when_trying_to_register_service_with_taken_type(RouterInterface $router)
    {
        $this->register('test', $router);

        $this
            ->shouldThrow('Sylius\Component\Registry\ExistingServiceException')
            ->duringRegister('test', $router)
        ;
    }

    public function it_throws_exception_when_trying_to_register_service_without_required_interface(
        RouteCompilerInterface $compiler
    ) {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringRegister('test', $compiler)
        ;
    }

    public function it_unregisters_service_with_given_type(RouterInterface $router)
    {
        $this->register('foo', $router);
        $this->has('foo')->shouldReturn(true);

        $this->unregister('foo');
        $this->has('foo')->shouldReturn(false);
    }

    public function it_retrieves_registered_service_by_type(RouterInterface $router)
    {
        $this->register('test', $router);
        $this->get('test')->shouldReturn($router);
    }

    public function it_throws_exception_if_trying_to_get_service_of_non_existing_type()
    {
        $this
            ->shouldThrow('Sylius\Component\Registry\NonExistingServiceException')
            ->duringGet('foo')
        ;
    }
}
