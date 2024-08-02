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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\TaxonImage;

use ApiPlatform\Doctrine\Orm\Extension\QueryResultItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\TaxonImage;

final class TaxonImageExtension implements QueryResultItemExtensionInterface
{
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
    }

    /** @param array<array-key, mixed> $context */
    public function supportsResult(string $resourceClass, ?Operation $operation = null, array $context = []): bool
    {
        return $operation instanceof Post && $resourceClass === TaxonImage::class;
    }

    /** @param array<array-key, mixed> $context */
    public function getResult(QueryBuilder $queryBuilder, ?string $resourceClass = null, ?Operation $operation = null, array $context = []): ?object
    {
        return $queryBuilder->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }
}
