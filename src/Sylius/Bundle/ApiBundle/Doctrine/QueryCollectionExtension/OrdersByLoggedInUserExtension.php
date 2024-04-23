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
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class OrdersByLoggedInUserExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, OrderInterface::class, true)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $stateParameterName = $queryNameGenerator->generateParameterName('state');

        $queryBuilder
            ->andWhere(sprintf('%s.state != :%s', $rootAlias, $stateParameterName))
            ->setParameter($stateParameterName, OrderInterface::STATE_CART)
        ;

        $user = $this->userContext->getUser();

        if ($user instanceof AdminUserInterface) {
            return;
        }

        if ($user instanceof ShopUserInterface) {
            /** @var CustomerInterface $customer */
            $customer = $user->getCustomer();

            $customerParameterName = $queryNameGenerator->generateParameterName('customer');

            $queryBuilder
                ->andWhere(sprintf('%s.customer = :%s', $rootAlias, $customerParameterName))
                ->setParameter($customerParameterName, $customer)
            ;

            return;
        }

        throw new AccessDeniedException();
    }
}
