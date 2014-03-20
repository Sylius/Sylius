<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxonomyBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $this->shouldHaveType('Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_form_with_name_and_permalink_and_description_fields(
        FormBuilder $builder,
        FormFactoryInterface $factory
    )
    {
        $builder->getFormFactory()->willReturn($factory);
        $builder->addEventSubscriber(Argument::type('Sylius\Bundle\TaxonomyBundle\Form\EventListener\BuildTaxonFormListener'))->shouldBeCalled();

        $builder->add('name', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('permalink', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);
        $builder->add('description', 'text', Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, array());
    }

    function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
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
