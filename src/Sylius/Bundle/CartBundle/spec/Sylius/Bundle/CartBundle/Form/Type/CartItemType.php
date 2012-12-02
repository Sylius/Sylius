<?php

namespace spec\Sylius\Bundle\CartBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

/**
 * Cart item form spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartItemType extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('CartItem');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Form\Type\CartItemType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_should_build_form_with_quantity_field($builder)
    {
        $builder
            ->add('quantity', 'integer', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_define_assigned_data_class($resolver)
    {
        $resolver->setDefaults(array('data_class' => 'CartItem'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
