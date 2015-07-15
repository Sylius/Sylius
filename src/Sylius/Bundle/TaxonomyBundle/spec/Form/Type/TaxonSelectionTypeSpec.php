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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\Taxon;
use Sylius\Component\Taxonomy\Model\Taxonomy;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_a_form(
        $taxonomyRepository,
        $taxonRepository,
        FormBuilderInterface $builder,
        DataTransformerInterface $dataTransformer,
        Taxonomy $taxonomy,
        Taxon $taxon
    ) {
        $taxonomyRepository->findAll()->shouldBeCalled()->willReturn(array($taxonomy));
        $taxonRepository->getTaxonsAsList($taxonomy)->shouldBeCalled()->willReturn(array($taxon));

        $taxonomy->getId()->shouldBeCalled()->willreturn(12);
        $taxonomy->getName()->shouldBeCalled()->willReturn('taxonomy name');
        $taxon->getId()->shouldBeCalled();
        $taxon->__toString()->shouldBeCalled()->willReturn('taxon name');

        $builder->addModelTransformer(Argument::any())->shouldBeCalled();

        $builder->add(12, 'choice', Argument::withKey('choice_list'))->shouldBeCalled();

        $this->buildForm($builder, array(
            'model_transformer' => array(
                'class' => $dataTransformer,
                'save_objects' => false,
            ),
            'multiple' => true,
        ));
    }

    function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => null,
            'multiple'           => true,
            'render_label'       => false,
            'model_transformer'  => 'Sylius\Bundle\TaxonomyBundle\Form\DataTransformer\TaxonSelectionToCollectionTransformer',
        ))->shouldBeCalled();

        $resolver->setNormalizers(Argument::withKey('model_transformer'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_taxon_selection');
    }
}
