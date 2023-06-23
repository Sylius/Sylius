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

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of ProductInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array|ProductInterface[]
     */
    public function findByName(string $name, string $locale): array;

    /**
     * @return array|ProductInterface[]
     */
    public function findByNamePart(string $phrase, string $locale, ?int $limit = null): array;

    public function findByPhrase(string $phrase, string $locale, ?int $limit = null): iterable;
}
