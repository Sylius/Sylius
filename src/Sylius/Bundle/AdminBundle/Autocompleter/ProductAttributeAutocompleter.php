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

namespace Sylius\Bundle\AdminBundle\Autocompleter;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\Autocomplete\OptionsAwareEntityAutocompleterInterface;

/**
 * @method mixed getGroupBy()
 */
final class ProductAttributeAutocompleter implements OptionsAwareEntityAutocompleterInterface
{
    private array $options = [];

    public function __construct (
        private readonly string $productAttributeClass,
    ) {
    }

    public function getEntityClass(): string
    {
        return $this->productAttributeClass;
    }

    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        $productAttributesToBeExcluded = $this->options['extra_options']['attributeCodes'];

        $qb = $repository->createQueryBuilder('o');

        $qb
            ->leftJoin('o.translations', 'translation')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->like('o.code', ':query'),
                $qb->expr()->like('translation.name', ':query')
            ))
            ->setParameter('query', '%' . $query . '%')
        ;

        if ($productAttributesToBeExcluded !== []) {
            $qb
                ->andWhere('o.code NOT IN (:productAttributesToBeExcluded)')
                ->setParameter('productAttributesToBeExcluded', $productAttributesToBeExcluded)
            ;
        }

        return $qb;
    }

    public function getLabel(object $entity): string
    {
        return $entity->getName();
    }

    public function getValue(object $entity): mixed
    {
        return $entity->getCode();
    }

    public function isGranted(Security $security): bool
    {
        return true;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method mixed getGroupBy()
    }
}