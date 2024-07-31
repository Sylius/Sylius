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

namespace Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Rule;

use Sylius\Bundle\AdminBundle\Form\Type\TaxonAutocompleteType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\TotalOfItemsFromTaxonConfigurationType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

final class TotalOfItemsFromTaxonConfigurationTypeExtension extends AbstractTypeExtension
{
    /** @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository */
    public function __construct(private readonly TaxonRepositoryInterface $taxonRepository)
    {
    }

    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxon', TaxonAutocompleteType::class, [
                'label' => 'sylius.form.promotion_rule.total_of_items_from_taxon.taxon',
            ])
            ->get('taxon')->addModelTransformer(
                new ReversedTransformer(new ResourceToIdentifierTransformer($this->taxonRepository, 'code')),
            )
        ;
    }

    /** @return iterable<class-string> */
    public static function getExtendedTypes(): iterable
    {
        return [TotalOfItemsFromTaxonConfigurationType::class];
    }
}
