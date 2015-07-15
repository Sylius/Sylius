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
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaxonChoiceTypeSpec extends ObjectBehavior
{
    function let(TaxonRepositoryInterface $taxonRepository)
    {
        $this->beConstructedWith($taxonRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->addModelTransformer(
            Argument::type('Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer')
        )->shouldBeCalled();

        $this->buildForm($builder, array(
            'multiple' => true
        ));
    }

    function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(Argument::withKey('choice_list'))->shouldBeCalled()->willReturn($resolver);
        $resolver->setRequired(array(
            'taxonomy',
            'filter',
        ))->shouldBeCalled()->willReturn($resolver);
        $resolver->setAllowedTypes(array(
            'taxonomy' => array('Sylius\Component\Taxonomy\Model\TaxonomyInterface'),
            'filter' => array('\Closure', 'null'),
        ))->shouldBeCalled()->willReturn($resolver);

        $this->setDefaultOptions($resolver, array());
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_taxon_choice');
    }

    function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }
}
