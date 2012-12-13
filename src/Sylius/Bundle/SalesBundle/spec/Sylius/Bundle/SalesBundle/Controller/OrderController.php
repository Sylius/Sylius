<?php

namespace spec\Sylius\Bundle\SalesBundle\Controller;

use PHPSpec2\ObjectBehavior;

/**
 * Order controller spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderController extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('sylius_sales', 'order', 'SyliusSalesBundle:Order');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Controller\OrderController');
    }

    function it_should_be_a_controller()
    {
        $this->shouldHaveType('Symfony\Bundle\FrameworkBundle\Controller\Controller');
    }

    function it_should_be_Sylius_resource_controller()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceController');
    }
}

