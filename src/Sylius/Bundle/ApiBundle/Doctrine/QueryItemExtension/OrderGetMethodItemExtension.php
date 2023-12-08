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
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

/** @experimental */
final class OrderGetMethodItemExtension implements QueryItemExtensionInterface
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

        if ($context[ContextKeys::HTTP_REQUEST_METHOD_TYPE] !== Request::METHOD_GET) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $user = $this->userContext->getUser();

        $this->applyToItemForGetMethod($user, $queryBuilder, $queryNameGenerator, $rootAlias);
    }

    private function applyToItemForGetMethod(
        ?UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $rootAlias,
    ): void {
        if ($user === null) {
            $queryBuilder
                ->leftJoin(sprintf('%s.customer', $rootAlias), 'customer')
                ->leftJoin('customer.user', 'user')
                ->andWhere($queryBuilder->expr()->orX(
                    'user IS NULL',
                    sprintf('%s.customer IS NULL', $rootAlias),
                    $queryBuilder->expr()->andX(
                        sprintf('%s.customer IS NOT NULL', $rootAlias),
                        sprintf('%s.createdByGuest = true', $rootAlias),
                    ),
                ))
            ;

            return;
        }

        if ($user instanceof ShopUserInterface && in_array('ROLE_USER', $user->getRoles(), true)) {
            $customerParameterName = $queryNameGenerator->generateParameterName('customer');

            $queryBuilder
                ->andWhere(sprintf('%s.customer = :%s', $rootAlias, $customerParameterName))
                ->setParameter($customerParameterName, $user->getCustomer()->getId())
            ;

            return;
        }

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            //admin has access to get all orders
            return;
        }

        throw new AccessDeniedHttpException('Requested method is not allowed.');
    }
}
