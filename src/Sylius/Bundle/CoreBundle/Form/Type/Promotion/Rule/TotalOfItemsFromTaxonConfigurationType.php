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

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TotalOfItemsFromTaxonConfigurationType extends AbstractType
{
    public function __construct(private RepositoryInterface $taxonRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxon', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.form.promotion_rule.total_of_items_from_taxon.taxon',
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'sylius.form.promotion_rule.total_of_items_from_taxon.amount',
                'currency' => $options['currency'],
            ])
        ;

        $builder->get('taxon')->addModelTransformer(
            new ReversedTransformer(new ResourceToIdentifierTransformer($this->taxonRepository, 'code')),
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('currency')
            ->setAllowedTypes('currency', 'string')
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_promotion_rule_total_of_items_from_taxon_configuration';
    }
}
