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
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Review\Model\ReviewInterface;

final class AcceptedProductReviewsExtension implements ContextAwareQueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private string $productReviewClass)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
        array $context = [],
    ): void {
        $this->applyAcceptedStatusCondition($queryBuilder, $queryNameGenerator, $resourceClass, $operationName);
    }

    /**
     * @param array<mixed> $identifiers
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?string $operationName = null,
        array $context = [],
    ): void {
        $this->applyAcceptedStatusCondition($queryBuilder, $queryNameGenerator, $resourceClass, $operationName);
    }

    private function applyAcceptedStatusCondition(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName,
    ): void {
        if ($this->productReviewClass !== $resourceClass) {
            return;
        }

        if ($operationName !== 'shop_get') {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $statusParameterName = $queryNameGenerator->generateParameterName('status');

        $queryBuilder
            ->andWhere(sprintf('%s.status = :%s', $rootAlias, $statusParameterName))
            ->setParameter($statusParameterName, ReviewInterface::STATUS_ACCEPTED)
        ;
    }
}
