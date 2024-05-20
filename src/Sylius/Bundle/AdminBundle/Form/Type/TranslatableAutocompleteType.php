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

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

final class TranslatableAutocompleteType extends AbstractType
{
    public const ENTITY_ALIAS = 'entity';

    public const TRANSLATION_ALIAS = 'translation';

    public function __construct(
        private readonly LocaleContextInterface $localeContext,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('entity_fields', ['code']);
        $resolver->setAllowedTypes('entity_fields', 'array');
        $resolver->setNormalizer('entity_fields', fn (Options $options, array $entityFields) => array_map(
            fn (string $field) => self::ENTITY_ALIAS . '.' . $field,
            $entityFields,
        ));

        $resolver->setDefault('translation_fields', ['name']);
        $resolver->setAllowedTypes('translation_fields', 'array');
        $resolver->setNormalizer('translation_fields', fn (Options $options, array $translationFields) => array_map(
            fn (string $field) => self::TRANSLATION_ALIAS . '.' . $field,
            $translationFields,
        ));

        $resolver->setDefault('locale_code', $this->localeContext->getLocaleCode());
        $resolver->setAllowedTypes('locale_code', ['string', 'null']);

        $resolver->setDefault('filter_query', null);
        $resolver->setNormalizer(
            'filter_query',
            fn (Options $options, ?callable $filterQuery) => $this->normalizeFilterQuery($options, $filterQuery),
        );
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_translatable_autocomplete';
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }

    private function normalizeFilterQuery(Options $options, ?callable $filterQuery): callable
    {
        return function (
            QueryBuilder $builder,
            string $query,
            EntityRepository $repository,
        ) use ($options, $filterQuery): void {
            $expr = $builder->expr();

            $entityConditions = self::getComparisons($expr, $options['entity_fields']);
            $translationConditions = self::getComparisons($expr, $options['translation_fields']);

            $builder
                ->innerJoin(
                    sprintf('%s.translations', self::ENTITY_ALIAS),
                    self::TRANSLATION_ALIAS,
                    Expr\Join::WITH,
                    sprintf('%s.locale = :locale', self::TRANSLATION_ALIAS),
                )
                ->andWhere(
                    $expr->orX(
                        ...$entityConditions,
                        ...$translationConditions,
                    ),
                )
                ->setParameter('locale', $options['locale_code'])
                ->setParameter('query', '%' . $query . '%')
            ;

            if (null !== $filterQuery) {
                $filterQuery($builder, $query, $repository);
            }
        };
    }

    /**
     * @param array<string> $fields
     *
     * @return iterable<Expr\Comparison>
     */
    private static function getComparisons(Expr $expr, array $fields): iterable
    {
        foreach ($fields as $field) {
            yield $expr->like($field, ':query');
        }
    }
}
