<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\ProductFilterConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\TaxonFilterConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionFilterCollectionType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class PromotionFilterCollectionTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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

    public static function getExtendedTypes(): iterable
    {
        return [PromotionFilterCollectionType::class];
    }
}
