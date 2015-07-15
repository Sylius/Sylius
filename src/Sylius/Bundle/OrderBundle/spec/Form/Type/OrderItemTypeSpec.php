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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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

    function it_builds_form_with_quantity_and_unit_price_fields(FormBuilderInterface $builder)
    {
        $builder
            ->add('quantity', 'integer', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('unitPrice', 'sylius_money', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
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
