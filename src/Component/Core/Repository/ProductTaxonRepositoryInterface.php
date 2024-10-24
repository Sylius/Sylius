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

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of ProductTaxonInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ProductTaxonRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilderForTaxon(string $locale, int|string $taxonId): QueryBuilder;

    public function findOneByProductCodeAndTaxonCode(string $productCode, string $taxonCode): ?ProductTaxonInterface;
}
