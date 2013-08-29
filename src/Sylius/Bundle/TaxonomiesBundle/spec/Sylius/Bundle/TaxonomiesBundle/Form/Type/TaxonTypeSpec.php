<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxonomiesBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxonTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Taxon', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     */
    function it_builds_form_with_name_and_permalink_and_description_fields($builder, $factory)
    {
        $builder->getFormFactory()->willReturn($factory);
        $builder->addEventSubscriber(Argument::type('Sylius\Bundle\TaxonomiesBundle\Form\EventListener\BuildTaxonFormListener'))->shouldBeCalled();

        $builder->add('name', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('permalink', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('description', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_defines_assigned_data_class($resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'Taxon',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->setDefaultOptions($resolver);
    }
}
