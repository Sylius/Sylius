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

namespace Sylius\Component\Taxonomy\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of TaxonInterface
 *
 * @extends RepositoryInterface<T>
 */
interface TaxonRepositoryInterface extends RepositoryInterface
{
    /** @return T[] */
    public function findChildren(string $parentCode, ?string $locale = null): array;

    /** @return T[] */
    public function findChildrenByChannelMenuTaxon(?TaxonInterface $menuTaxon = null, ?string $locale = null): array;

    /** @return T[] */
    public function findRootNodes(): array;

    /** @return T[] */
    public function findHydratedRootNodes(): array;

    public function findOneBySlug(string $slug, string $locale): ?TaxonInterface;

    /** @return T[] */
    public function findByName(string $name, string $locale): array;

    /**
     * @param array<string>|null $excludes
     *
     * @return T[]
     */
    public function findByNamePart(string $phrase, ?string $locale = null, ?int $limit = null, ?array $excludes = null): array;

    public function createListQueryBuilder(): QueryBuilder;
}
