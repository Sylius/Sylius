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

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;

#[AsEntityAutocompleteField(
    alias: 'sylius_admin_taxon',
    route: 'sylius_admin_entity_autocomplete',
)]
final class TaxonAutocompleteType extends AbstractType
{
    public function __construct(
        private readonly string $taxonClass,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->taxonClass,
        ]);

        $resolver->setDefault('choice_label', function (Options $options): string {
            return $options['extra_options']['choice_label'] ?? 'fullname';
        });

        $resolver->setAllowedTypes('extra_options', 'array');

        $resolver->setNormalizer('filter_query', function (Options $options, ?callable $filterQuery): ?callable {
            /** @var array<string, mixed> $extraOptions */
            $extraOptions = $options['extra_options'];
            /** @var array<string> $excludedCodes */
            $excludedCodes = $extraOptions['excluded_taxon_codes'] ?? [];

            if (empty($excludedCodes)) {
                return $filterQuery;
            }

            return function (QueryBuilder $queryBuilder, string $query, EntityRepository $repository) use ($filterQuery, $excludedCodes): void {
                if (null !== $filterQuery) {
                    $filterQuery($queryBuilder, $query, $repository);
                }

                $queryBuilder
                    ->andWhere('entity.code NOT IN (:excluded_codes)')
                    ->setParameter('excluded_codes', $excludedCodes)
                ;
            };
        });
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_taxon_autocomplete';
    }

    public function getParent(): string
    {
        return TranslatableAutocompleteType::class;
    }
}
