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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

/** @experimental */
final class OrderVisitorItemExtension implements QueryItemExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = [],
    ) {
        if (!is_a($resourceClass, OrderInterface::class, true)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $user = $this->userContext->getUser();

        if ($user !== null) {
            return;
        }

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

        if ($httpRequestMethodType === Request::METHOD_GET || $operationName === 'shop_select_payment_method') {
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
