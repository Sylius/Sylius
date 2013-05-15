<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;

/**
 * Resource controller spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResourceControllerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param Symfony\Component\HttpFoundation\Request                 $request
     * @param Symfony\Component\HttpFoundation\ParameterBag            $attributes
     */
    function let($container, $request, $attributes)
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
