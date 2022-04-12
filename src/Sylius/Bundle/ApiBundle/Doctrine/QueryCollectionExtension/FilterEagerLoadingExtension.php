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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\Product;

/**
 * @experimental
 * This class decorates api_platform.doctrine.orm.query_extension.filter_eager_loading.
 * It is a workaround for https://github.com/api-platform/core/issues/2253.
 */
final class FilterEagerLoadingExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public function __construct(private ContextAwareQueryCollectionExtensionInterface $decoratedExtension)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = [])
    {
        if (Product::class === $resourceClass && 'shop_get' === $operationName) {
            return;
        }

        $this->decoratedExtension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
    }
}
