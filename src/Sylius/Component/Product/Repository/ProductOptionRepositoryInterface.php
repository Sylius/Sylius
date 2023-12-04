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

namespace Sylius\Component\Product\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of ProductOptionInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ProductOptionRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(string $locale): QueryBuilder;

    /**
     * @return ProductOptionInterface[]
     */
    public function findByName(string $name, string $locale): array;

    /**
     * @return ProductOptionInterface[]
     */
    public function findByPhrase(string $phrase, string $locale, int $limit = 10): array;

    /**
     * @param string[] $codes
     *
     * @return ProductOptionInterface[]
     */
    public function findByCodes(array $codes = []): array;
}
