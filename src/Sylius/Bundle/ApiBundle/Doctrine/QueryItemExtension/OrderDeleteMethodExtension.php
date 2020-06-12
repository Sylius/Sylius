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
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

final class OrderDeleteMethodExtension implements QueryItemExtensionInterface
{
    /** @var UserContextInterface */
    private $userContext;

    public function __construct(UserContextInterface $userContext)
    {
        $this->userContext = $userContext;
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ) {
        $operationName = strtoupper($operationName);

        if (!is_a($resourceClass, OrderInterface::class, true)) {
            return;
        }

        if ($operationName !== Request::METHOD_DELETE) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $user = $this->userContext->getUser();

        $this->applyToItemForDeleteMethod($user, $queryBuilder, $operationName, $rootAlias);
    }

    private function applyToItemForDeleteMethod(
        ?UserInterface $user,
        QueryBuilder $queryBuilder,
        string $operationName,
        string $rootAlias
    ): void {
        if ($user === null) {
            $queryBuilder
                ->andWhere(sprintf('%s.customer IS NULL', $rootAlias))
                ->andWhere(sprintf('%s.state = :state', $rootAlias))
                ->setParameter('state', OrderInterface::STATE_CART)
            ;

            return;
        }

        if ($user instanceof ShopUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            $queryBuilder
                ->andWhere(sprintf('%s.customer = :customer', $rootAlias))
                ->setParameter('customer', $user->getCustomer()->getId())
                ->andWhere(sprintf('%s.state = :state', $rootAlias))
                ->setParameter('state', OrderInterface::STATE_CART)
            ;

            return;
        }

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            $queryBuilder
                ->andWhere(sprintf('%s.state = :state', $rootAlias))
                ->setParameter('state', OrderInterface::STATE_CART)
            ;

            return;
        }

        throw new AccessDeniedHttpException('Requested method is not allowed.');
    }
}
