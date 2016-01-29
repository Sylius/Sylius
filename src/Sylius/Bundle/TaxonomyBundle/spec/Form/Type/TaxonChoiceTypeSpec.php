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
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->addModelTransformer(
            Argument::type(CollectionToArrayTransformer::class)
        )->shouldBeCalled();

        $this->buildForm($builder, [
            'multiple' => true,
        ]);
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(Argument::withKey('choice_list'))->shouldBeCalled()->willReturn($resolver);
        $resolver->setDefaults(Argument::withKey('taxonomy'))->shouldBeCalled()->willReturn($resolver);
        $resolver->setDefaults(Argument::withKey('filter'))->shouldBeCalled()->willReturn($resolver);

        $resolver->setAllowedTypes('taxonomy', [TaxonomyInterface::class, 'null'])->shouldBeCalled()->willReturn($resolver);
        $resolver->setAllowedTypes('filter', ['callable', 'null'])->shouldBeCalled()->willReturn($resolver);

        $this->configureOptions($resolver, []);
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
