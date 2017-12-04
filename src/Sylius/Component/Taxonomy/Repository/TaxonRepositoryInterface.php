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

namespace Sylius\Component\Taxonomy\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface TaxonRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $parentCode
     * @param string|null $locale
     *
     * @return array|TaxonInterface[]
     */
    public function findChildren(string $parentCode, ?string $locale = null): array;

    /**
     * @return array|TaxonInterface[]
     */
    public function findRootNodes(): array;

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return TaxonInterface|null
     */
    public function findOneBySlug(string $slug, string $locale): ?TaxonInterface;

    /**
     * @param string $name
     * @param string $locale
     *
     * @return array|TaxonInterface[]
     */
    public function findByName(string $name, string $locale): array;

    /**
     * @param string $phrase
     * @param string|null $locale
     *
     * @return array|TaxonInterface[]
     */
    public function findByNamePart(string $phrase, ?string $locale = null): array;

    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder(): QueryBuilder;
}
