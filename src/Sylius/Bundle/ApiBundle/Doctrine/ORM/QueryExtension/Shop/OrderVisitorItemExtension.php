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

use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class OrderVisitorItemExtension implements QueryItemExtensionInterface
{
    /**
     * @param array<array-key, mixed> $nonFilteredCartAllowedOperations
     */
    public function __construct(
        private UserContextInterface $userContext,
        private array $nonFilteredCartAllowedOperations = [],
    ) {
    }

    /**
     * @param array<array-key, mixed> $identifiers
     * @param array<array-key, mixed> $context
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, OrderInterface::class, true) || null === $operation) {
            return;
        }

        $user = $this->userContext->getUser();
        if ($user !== null) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->leftJoin(sprintf('%s.customer', $rootAlias), 'customer')
            ->leftJoin('customer.user', 'user')
            ->andWhere($queryBuilder->expr()->orX(
                'user IS NULL',
                sprintf('%s.customer IS NULL', $rootAlias),
                $queryBuilder->expr()->andX(
                    sprintf('%s.customer IS NOT NULL', $rootAlias),
                    sprintf('%s.createdByGuest = :createdByGuest', $rootAlias),
                ),
            ))->setParameter('createdByGuest', true)
        ;

        $httpRequestMethodType = $context[ContextKeys::HTTP_REQUEST_METHOD_TYPE];

        if ($httpRequestMethodType === Request::METHOD_GET || in_array($operation->getName(), $this->nonFilteredCartAllowedOperations, true)) {
            return;
        }

        $this->filterCart($queryBuilder, $queryNameGenerator, $rootAlias);
    }

    private function filterCart(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $rootAlias): void
    {
        $stateParameterName = $queryNameGenerator->generateParameterName('state');

        $queryBuilder
            ->andWhere(sprintf('%s.state = :%s', $rootAlias, $stateParameterName))
            ->setParameter($stateParameterName, OrderInterface::STATE_CART)
        ;
    }
}