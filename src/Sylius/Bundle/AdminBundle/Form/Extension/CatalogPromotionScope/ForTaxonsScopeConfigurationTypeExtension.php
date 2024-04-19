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

namespace Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionScope;

use Sylius\Bundle\AdminBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForTaxonsScopeConfigurationType;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class ForTaxonsScopeConfigurationTypeExtension extends AbstractTypeExtension
{
    /** @param DataTransformerInterface<TaxonInterface, string|null> $taxonsToCodesTransformer */
    public function __construct(private readonly DataTransformerInterface $taxonsToCodesTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('taxons', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.ui.taxons',
                'multiple' => true,
                'required' => false,
            ])
            ->get('taxons')->addModelTransformer($this->taxonsToCodesTransformer)
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        yield ForTaxonsScopeConfigurationType::class;
    }
}
