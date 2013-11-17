<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resource controller spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResourceControllerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container, Request $request, ParameterBag $attributes)
    {
        $this->beConstructedWith('sylius_resource', 'test', 'SyliusResourceBundle:Test');

        $request->attributes = $attributes;
        $container->get('request')->willReturn($request);

        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceController');
    }

    function it_is_a_controller()
    {
        $this->shouldHaveType('Symfony\Bundle\FrameworkBundle\Controller\Controller');
    }
}
