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

namespace Sylius\Component\Product\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ProductOptionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $locale
     *
     * @return QueryBuilder
     */
    public function createListQueryBuilder(string $locale): QueryBuilder;

    /**
     * @param string $name
     * @param string $locale
     *
     * @return array|ProductOptionInterface[]
     */
    public function findByName(string $name, string $locale): array;
}
