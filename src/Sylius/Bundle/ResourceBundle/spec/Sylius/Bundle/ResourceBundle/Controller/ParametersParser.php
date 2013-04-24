<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PHPSpec2\ObjectBehavior;

/**
 * Parameters parser spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ParametersParser extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ParametersParser');
    }

    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    function it_replaces_parameter_values_starting_with_dollar_sign_by_request_values($request)
    {
        $parameters = array(
            'template' => 'SyliusAssortmentBundle:Product:custom.html.twig',
            'criteria' => array('slug' => '$slug'),
            'redirect' => array(
                'route'      => 'sylius_product_index',
                'parameters' => array('view' => '$view')
            )
        );

        $request->get('slug')->shouldBeCalled()->willReturn('super-product-slug');
        $request->get('view')->shouldBeCalled()->willReturn('grid');

        $expected = array(
            'template' => 'SyliusAssortmentBundle:Product:custom.html.twig',
            'criteria' => array('slug' => 'super-product-slug'),
            'redirect' => array(
                'route'      => 'sylius_product_index',
                'parameters' => array('view' => 'grid')
            )
        );

        $this->parse($parameters, $request)->shouldReturn($expected);
    }
}
