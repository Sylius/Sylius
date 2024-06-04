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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Symfony\Component\HttpFoundation\Request;

final class OrderVisitorItemExtension implements QueryItemExtensionInterface
{
    public function __construct(
        private UserContextInterface $userContext,
        private array $nonFilteredCartAllowedOperations = [],
    ) {
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?string $operationName = null,
        array $context = [],
    ) {
        if (!is_a($resourceClass, OrderInterface::class, true)) {
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
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->isNotNull('user'),
                        $queryBuilder->expr()->neq(sprintf('%s.checkoutState', $rootAlias), ':checkoutState'),
                        $queryBuilder->expr()->eq(sprintf('%s.createdByGuest', $rootAlias), ':createdByGuest'),
                    ),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->isNull('user'),
                        $queryBuilder->expr()->eq(sprintf('%s.createdByGuest', $rootAlias), ':createdByGuest'),
                    ),
                ),
            )->setParameter('createdByGuest', true)
            ->setParameter('checkoutState', OrderCheckoutStates::STATE_COMPLETED)
        ;

        $httpRequestMethodType = $context[ContextKeys::HTTP_REQUEST_METHOD_TYPE];

        if ($httpRequestMethodType === Request::METHOD_GET || in_array($operationName, $this->nonFilteredCartAllowedOperations, true)) {
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
