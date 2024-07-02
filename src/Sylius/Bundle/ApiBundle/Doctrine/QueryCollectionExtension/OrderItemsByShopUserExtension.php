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
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class OrderItemsByShopUserExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    /** @param array<string, mixed> $context */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, OrderItemInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $orderParameterName = $queryNameGenerator->generateParameterName('order');
        $customerJoinParameterName = $queryNameGenerator->generateJoinAlias('customer_join');
        $customerParameterName = $queryNameGenerator->generateParameterName('customer');

        $queryBuilder
            ->leftJoin(sprintf('%s.order', $rootAlias), $orderParameterName)
            ->leftJoin(sprintf('%s.customer', $orderParameterName), $customerJoinParameterName)
            ->andWhere(sprintf('%s = :%s', $customerJoinParameterName, $customerParameterName))
            ->setParameter($customerParameterName, $user->getCustomer()->getId())
            ->addOrderBy(sprintf('%s.id', $rootAlias), 'ASC')
        ;
    }
}
