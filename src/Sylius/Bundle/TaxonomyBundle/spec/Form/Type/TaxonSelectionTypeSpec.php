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
use Sylius\Bundle\TaxonomyBundle\Form\DataTransformer\TaxonSelectionToCollectionTransformer;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\Taxon;
use Sylius\Component\Taxonomy\Model\Taxonomy;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonSelectionTypeSpec extends ObjectBehavior
{
    function let(RepositoryInterface $taxonomyRepository, TaxonRepositoryInterface $taxonRepository)
    {
        $this->beConstructedWith($taxonomyRepository, $taxonRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonSelectionType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_a_form(
        $taxonomyRepository,
        $taxonRepository,
        FormBuilderInterface $builder,
        DataTransformerInterface $dataTransformer,
        Taxonomy $taxonomy,
        Taxon $taxon
    ) {
        $taxonomyRepository->findAll()->shouldBeCalled()->willReturn([$taxonomy]);
        $taxonRepository->getTaxonsAsList($taxonomy)->shouldBeCalled()->willReturn([$taxon]);

        $taxonomy->getId()->shouldBeCalled()->willreturn(12);
        $taxonomy->getName()->shouldBeCalled()->willReturn('taxonomy name');
        $taxon->getId()->shouldBeCalled();
        $taxon->__toString()->shouldBeCalled()->willReturn('taxon name');

        $builder->addModelTransformer(Argument::any())->shouldBeCalled();

        $builder->add(12, 'choice', Argument::withKey('choice_list'))->shouldBeCalled();

        $this->buildForm($builder, [
            'model_transformer' => [
                'class' => $dataTransformer,
                'save_objects' => false,
            ],
            'multiple' => true,
        ]);
    }

    function it_is_a_form()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'multiple' => true,
            'render_label' => false,
            'model_transformer' => TaxonSelectionToCollectionTransformer::class,
        ])->shouldBeCalled();

        $resolver->setNormalizer('model_transformer', Argument::type('callable'))->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_taxon_selection');
    }
}
