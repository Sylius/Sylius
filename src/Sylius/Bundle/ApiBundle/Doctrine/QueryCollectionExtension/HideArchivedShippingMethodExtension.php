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

final class HideArchivedShippingMethodExtension implements ContextAwareQueryCollectionExtensionInterface
{
    /** @var string */
    private $shippingMethodClass;

    public function __construct(string $shippingMethodClass)
    {
        $this->shippingMethodClass = $shippingMethodClass;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        if ($this->shippingMethodClass !== $resourceClass) {
            return;
        }

        if (isset($context['filters']['exists']['archivedAt'])) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(sprintf('%s.archivedAt IS NULL', $rootAlias));
    }
}
