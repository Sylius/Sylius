<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderItemTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('OrderItem', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Form\Type\OrderItemType');
    }

    function it_is_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_builds_form_with_quantity_and_unit_price_fields($builder)
    {
        $builder->add('quantity', 'integer', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('unitPrice', 'money', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_defines_assigned_data_class($resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'OrderItem',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->setDefaultOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_order_item');
    }
}
