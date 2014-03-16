<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Registry;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Routing\RouteCompilerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ServiceRegistrySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Symfony\Component\Routing\RouterInterface');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Registry\ServiceRegistry');
    }

    function it_implements_service_registry_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Registry\ServiceRegistryInterface');
    }

    function it_initializes_services_array_by_default()
    {
        $this->all()->shouldReturn(array());
    }

    function it_registers_service_with_given_type(RouterInterface $router)
    {
        $this->has('test')->shouldReturn(false);
        $this->register('test', $router);

        $this->has('test')->shouldReturn(true);
        $this->get('test')->shouldReturn($router);
    }

    function it_throws_exception_when_trying_to_register_service_with_taken_type(RouterInterface $router)
    {
        $this->register('test', $router);

        $this
            ->shouldThrow('Sylius\Bundle\ResourceBundle\Registry\ExistingServiceException')
            ->duringRegister('test', $router)
        ;
    }

    function it_throws_exception_when_trying_to_register_service_without_required_interface(RouteCompilerInterface $compiler)
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringRegister('test', $compiler)
        ;
    }

    function it_unregisters_service_with_given_type(RouterInterface $router)
    {
        $this->register('foo', $router);
        $this->has('foo')->shouldReturn(true);

        $this->unregister('foo');
        $this->has('foo')->shouldReturn(false);
    }

    function it_retrieves_registered_service_by_type(RouterInterface $router)
    {
        $this->register('test', $router);
        $this->get('test')->shouldReturn($router);
    }

    function it_throws_exception_if_trying_to_get_service_of_non_existing_type()
    {
        $this
            ->shouldThrow('Sylius\Bundle\ResourceBundle\Registry\NonExistingServiceException')
            ->duringGet('foo')
        ;
    }
}
