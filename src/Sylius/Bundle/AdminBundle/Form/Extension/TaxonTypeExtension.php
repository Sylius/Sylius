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

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\AdminBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class TaxonTypeExtension extends AbstractTypeExtension
{
    /**
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $taxonId = $builder->getData()->getId();

        $builder
            ->add('parent', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.form.taxon.parent',
                'extra_options' => [
                    'excluded_taxons' => null !== $taxonId ? [$taxonId] : [],
                ],
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [TaxonType::class];
    }
}
