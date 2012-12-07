<?php

namespace spec\Sylius\Bundle\TaxonomiesBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

/**
 * Taxonomy form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxonomyType extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Taxonomy');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonomyType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_define_assigned_data_class($resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Taxonomy'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
