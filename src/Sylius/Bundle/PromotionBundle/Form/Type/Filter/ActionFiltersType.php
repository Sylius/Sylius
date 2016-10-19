<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\Type\Filter;

use Sylius\Bundle\CoreBundle\Form\DataTransformer\TaxonsToCodesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonChoiceType;

/**
 * @author Viorel Craescu <viorel@craescu.com>
 * @author Gabi Udrescu <gabriel.udr@gmail.com>
 */
class ActionFiltersType extends AbstractType
{
    /**
     * @var TaxonsToCodesTransformer
     */
    private $transformer;

    /**
     * @param TaxonsToCodesTransformer $transformer
     */
    public function __construct(TaxonsToCodesTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'taxons',
                TaxonChoiceType::class,
                [
                    'multiple' => true,
                ]
            )
            ->add('price_range', PriceRangeType::class);

        $builder->get('taxons')->addModelTransformer($this->transformer);
    }
}
