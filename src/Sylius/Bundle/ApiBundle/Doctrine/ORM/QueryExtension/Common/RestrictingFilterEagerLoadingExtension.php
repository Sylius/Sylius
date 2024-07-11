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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

/**
 * This class decorates api_platform.doctrine.orm.query_extension.filter_eager_loading.
 * It is a workaround for https://github.com/api-platform/core/issues/2253.
 */
final readonly class RestrictingFilterEagerLoadingExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private QueryCollectionExtensionInterface $decoratedExtension, private array $restrictedResources)
    {
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if ($this->isOperationRestricted($resourceClass, $operation)) {
            return;
        }

        $this->decoratedExtension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operation, $context);
    }

    private function isOperationRestricted(string $resourceClass, Operation $operation): bool
    {
        return $this->restrictedResources[$resourceClass]['operations'][$operation->getName()]['enabled'] ?? false;
    }
}
