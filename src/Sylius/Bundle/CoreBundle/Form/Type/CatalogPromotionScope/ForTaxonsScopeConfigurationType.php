<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope;

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ForTaxonsScopeConfigurationType extends AbstractType
{
    public function __construct(private DataTransformerInterface $taxonsToCodesTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('taxons', TaxonAutocompleteChoiceType::class, [
            'label' => 'sylius.ui.taxons',
            'multiple' => true,
            'required' => false,
            'choice_value' => 'code',
            'resource' => 'sylius.taxon',
            'constraints' => [
                new NotBlank(['groups' => 'sylius', 'message' => 'sylius.catalog_promotion_scope.for_taxons.not_empty'])
            ],
        ]);

        $builder->get('taxons')->addModelTransformer($this->taxonsToCodesTransformer);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion_scope_taxon_configuration';
    }
}
