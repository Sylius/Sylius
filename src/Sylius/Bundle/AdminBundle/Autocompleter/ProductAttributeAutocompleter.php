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
use Symfony\UX\Autocomplete\EntityAutocompleterInterface;

final readonly class ProductAttributeAutocompleter implements EntityAutocompleterInterface
{
    public function __construct (
        private string $productAttributeClass,
    ) {
    }

    public function getEntityClass(): string
    {
        return $this->productAttributeClass;
    }

    public function createFilteredQueryBuilder(EntityRepository $repository, string $query): QueryBuilder
    {
        return $repository->createQueryBuilder('o')
            ->andWhere('o.code LIKE :query')
            ->setParameter('query', '%' . $query . '%')
        ;
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
}
