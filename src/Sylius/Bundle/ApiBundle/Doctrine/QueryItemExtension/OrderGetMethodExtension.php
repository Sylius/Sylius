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
use Sylius\Bundle\ApiBundle\Helper\UserContextHelperInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

final class OrderGetMethodExtension implements QueryItemExtensionInterface
{
    /** @var UserContextHelperInterface */
    private $userContextHelper;

    public function __construct(UserContextHelperInterface $userContextHelper)
    {
        $this->userContextHelper = $userContextHelper;
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ) {
        if (!is_a($resourceClass, OrderInterface::class, true)) {
            return;
        }

        if ($operationName !== Request::METHOD_GET) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $this->applyToItemForGetMethod($this->userContextHelper->getUser(), $queryBuilder, $operationName, $rootAlias);
    }

    private function applyToItemForGetMethod(
        ?UserInterface $user,
        QueryBuilder $queryBuilder,
        string $operationName,
        string $rootAlias
    ): void {
        if ($this->userContextHelper->isVisitor()) {
            $queryBuilder->andWhere(sprintf('%s.customer IS NULL', $rootAlias));

            return;
        }

        if ($this->userContextHelper->hasShopUserRoleApiAccess()) {
            /** @var CustomerInterface $customer */
            $customer = $user->getCustomer();

            $queryBuilder
                ->andWhere(sprintf('%s.customer = :customer', $rootAlias))
                ->setParameter('customer', $customer->getId())
            ;

            return;
        }

        if ($this->userContextHelper->hasAdminRoleApiAccess()) {
            //admin has access to get all orders
            return;
        }

        throw new AccessDeniedHttpException('Requested method is not allowed.');
    }
}
