<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\ProductFilterConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\TaxonFilterConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionFilterCollectionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PromotionFilterCollectionTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('taxons_filter', TaxonFilterConfigurationType::class, [
            'label' => false,
            'required' => false,
        ]);
        $builder->add('products_filter', ProductFilterConfigurationType::class, [
            'label' => false,
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return PromotionFilterCollectionType::class;
    }
}
