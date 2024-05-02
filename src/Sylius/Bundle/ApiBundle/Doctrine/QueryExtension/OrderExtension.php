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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderExtension implements ContextAwareQueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * @param array<string> $orderStatesToFilterOut
     */
    public function __construct(
        private SectionProviderInterface $sectionProvider,
        private array $orderStatesToFilterOut,
    ) {
    }

    /**
     * @param array<mixed> $context
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
        array $context = [],
    ): void {
        $this->filterOutOrders($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    /**
     * @param array<mixed> $context
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
        $this->filterOutOrders($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    private function filterOutOrders(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        if (!is_a($resourceClass, OrderInterface::class, true)) {
            return;
        }

        if (!$this->sectionProvider->getSection() instanceof AdminApiSection) {
            return;
        }

        $stateParameter = $queryNameGenerator->generateParameterName('state');
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere($queryBuilder->expr()->notIn(sprintf('%s.state', $rootAlias), sprintf(':%s', $stateParameter)))
            ->setParameter($stateParameter, $this->orderStatesToFilterOut, ArrayParameterType::STRING)
        ;
    }
}
