<?php

namespace spec\Sylius\Bundle\PromotionBundle\Form\Type\Filter;

use Sylius\Bundle\PromotionBundle\Form\Type\Filter\ActionFiltersType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\PromotionBundle\Form\Type\Filter\PriceRangeType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonChoiceType;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\TaxonsToCodesTransformer;

class ActionFiltersTypeSpec extends ObjectBehavior
{
    public function let(TaxonsToCodesTransformer $transformer)
    {
        $this->beConstructedWith($transformer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ActionFiltersType::class);
    }

    public function it_is_an_abstract_form()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    public function it_should_have_price_range_and_taxon_filters(FormBuilderInterface $builder, FormBuilderInterface $taxons, TaxonsToCodesTransformer $transformer)
    {
        $builder
            ->add('price_range', PriceRangeType::class)
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('taxons', TaxonChoiceType::class, Argument::exact([
                'multiple' => true,
            ]))
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder->get('taxons')->willReturn($taxons);

        $taxons->addModelTransformer($transformer)->shouldBeCalled();

        $this->buildForm($builder, []);
    }
}
