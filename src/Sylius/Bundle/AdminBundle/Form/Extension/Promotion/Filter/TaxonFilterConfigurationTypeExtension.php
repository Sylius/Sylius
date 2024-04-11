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

namespace Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Filter;

use Sylius\Bundle\AdminBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\TaxonFilterConfigurationType;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class TaxonFilterConfigurationTypeExtension extends AbstractTypeExtension
{
    /** @param DataTransformerInterface<TaxonInterface, string|null> $taxonsToCodesTransformer */
    public function __construct(private readonly DataTransformerInterface $taxonsToCodesTransformer)
    {
    }

    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxons', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.form.promotion_filter.taxons',
                'multiple' => true,
                'required' => false,
            ])
            ->get('taxons')->addModelTransformer($this->taxonsToCodesTransformer)
        ;
    }

    /** @return iterable<class-string> */
    public static function getExtendedTypes(): iterable
    {
        return [TaxonFilterConfigurationType::class];
    }
}
