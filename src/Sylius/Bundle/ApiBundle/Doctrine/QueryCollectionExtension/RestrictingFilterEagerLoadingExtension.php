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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * This class decorates api_platform.doctrine.orm.query_extension.filter_eager_loading.
 * It is a workaround for https://github.com/api-platform/core/issues/2253.
 */
final class RestrictingFilterEagerLoadingExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public function __construct(private ContextAwareQueryCollectionExtensionInterface $decoratedExtension, private array $restrictedResources)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null, array $context = []): void
    {
        if ($this->isOperationRestricted($resourceClass, $operationName)) {
            return;
        }

        $this->decoratedExtension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
    }

    private function isOperationRestricted(string $resourceClass, string $operationName): bool
    {
        return $this->restrictedResources[$resourceClass]['operations'][$operationName]['enabled'] ?? false;
    }
}
