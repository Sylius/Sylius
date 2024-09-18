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

final readonly class NonArchivedExtension implements QueryCollectionExtensionInterface
{
    /** @param array<int, string> $nonArchivedClasses */
    public function __construct(private array $nonArchivedClasses)
    {
    }

    /** @param array<array-key, mixed> $context */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (!in_array($resourceClass, $this->nonArchivedClasses, true)) {
            return;
        }

        if (isset($context['filters']['exists']['archivedAt'])) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere($queryBuilder->expr()->isNull(sprintf('%s.archivedAt', $rootAlias)));
    }
}
