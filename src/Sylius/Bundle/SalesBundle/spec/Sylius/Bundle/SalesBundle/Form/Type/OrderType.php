<?php

namespace spec\Sylius\Bundle\SalesBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

/**
 * Order form type spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderType extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Order');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Form\Type\OrderType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_should_build_a_form_with_items_collection_field($builder)
    {
        $builder->add('items', 'collection', ANY_ARGUMENT)->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_define_assigned_data_class($resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Order'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
