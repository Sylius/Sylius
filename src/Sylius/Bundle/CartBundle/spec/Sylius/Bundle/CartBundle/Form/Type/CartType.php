<?php

namespace spec\Sylius\Bundle\CartBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

/**
 * Cart form spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartType extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Cart');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Form\Type\CartType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_should_build_form_with_items_collection($builder)
    {
        $builder
            ->add('items', 'collection', array('type' => 'sylius_cart_item'))
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
        $resolver->setDefaults(array('data_class' => 'Cart'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
