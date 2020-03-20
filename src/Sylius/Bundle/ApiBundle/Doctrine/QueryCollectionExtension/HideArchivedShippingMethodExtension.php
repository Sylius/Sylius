<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class HideArchivedShippingMethodExtension implements QueryCollectionExtensionInterface
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
        string $operationName = null
    ) {
        if ($this->shippingMethodClass !== $resourceClass) {
            return;
        }

        $rootAlias  = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere(sprintf('%s.archivedAt IS NULL', $rootAlias));
    }
}
