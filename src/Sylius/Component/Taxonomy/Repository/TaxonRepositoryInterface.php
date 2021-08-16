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
     * @return array|TaxonInterface[]
     */
    public function findChildren(string $parentCode, ?string $locale = null): array;

    public function findChildrenByChannelMenuTaxon(?TaxonInterface $menuTaxon = null, ?string $locale = null): array;

    /**
     * @return array|TaxonInterface[]
     */
    public function findRootNodes(): array;

    public function findOneBySlug(string $slug, string $locale): ?TaxonInterface;

    /**
     * @return array|TaxonInterface[]
     */
    public function findByName(string $name, string $locale): array;

    /**
     * @return array|TaxonInterface[]
     */
    public function findByNamePart(string $phrase, ?string $locale = null, ?int $limit = null): array;

    public function createListQueryBuilder(): QueryBuilder;
}
