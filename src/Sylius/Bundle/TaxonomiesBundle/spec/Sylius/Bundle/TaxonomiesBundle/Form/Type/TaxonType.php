<?php

namespace spec\Sylius\Bundle\TaxonomiesBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

/**
 * Taxon form spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxonType extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Taxon');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_should_build_form_with_name_and_permalink_fields($builder)
    {
        $builder->add('name', 'text')->shouldBeCalled()->willReturn($builder);
        $builder->add('permalink', 'text')->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_define_assigned_data_class($resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Taxon'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
