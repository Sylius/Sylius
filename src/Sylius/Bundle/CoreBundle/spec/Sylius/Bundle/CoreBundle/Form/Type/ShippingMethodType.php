<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

class ShippingMethodType extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistryInterface $registry
     */
    function let($registry)
    {
        $this->beConstructedWith('ShippingMethod', $registry);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\ShippingMethodType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_should_extend_Sylius_shipping_method_form_type()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder          $builder
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     */
    function it_should_build_form_with_zone_choice_field($builder, $factory)
    {
        $builder->getFormFactory()->willReturn($factory);

        $builder->add('zone', 'sylius_zone_choice', array(
            'label' => 'sylius.form.shipping_method.zone'
        ))->willReturn($builder)->shouldBeCalled();

        $this->buildForm($builder, array());
    }
}
