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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Review\Model\ReviewInterface;

final readonly class AcceptedProductReviewsExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private string $productReviewClass)
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
        if ($this->productReviewClass !== $resourceClass || null === $operation) {
            return;
        }

        if ($operation->getName() !== 'shop_get') {
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
