<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion;

use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\ProductFilterConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\TaxonFilterConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionFiltersType as BasePromotionFiltersType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class PromotionFiltersType extends BasePromotionFiltersType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('taxons_filter', TaxonFilterConfigurationType::class, ['required' => false]);
        $builder->add('products_filter', ProductFilterConfigurationType::class, ['required' => false]);
    }
}
