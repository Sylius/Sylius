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
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ProductAssociationTypeRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(string $locale): QueryBuilder;

    /**
     * @return array|ProductAssociationTypeInterface[]
     */
    public function findByName(string $name, string $locale): array;
}
