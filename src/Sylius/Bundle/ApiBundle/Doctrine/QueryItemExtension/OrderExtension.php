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

final class OrderExtension implements QueryItemExtensionInterface
{
    /** @var UserContextInterface */
    private $userContext;

    public function __construct(UserContextInterface $userContext)
    {
        $this->userContext = $userContext;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, OrderInterface::class, true);
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

        $operationName = strtoupper($operationName);
        if ($operationName === Request::METHOD_DELETE || $operationName === Request::METHOD_GET) {
            $rootAlias = $queryBuilder->getRootAliases()[0];

            $user = $this->userContext->getUser();

            if ($user === null) {
                $queryBuilder->andWhere(sprintf('%s.customer IS NULL', $rootAlias));

                return;
            }

            if ($user !== null && $user instanceof ShopUserInterface) {
                $queryBuilder
                    ->andWhere(sprintf('%s.customer = :customerId', $rootAlias))
                    ->setParameter('customerId', $user->getCustomer()->getId())
                ;

                return;
            }

            if ($user !== null && $user instanceof AdminUserInterface) {
                //admin has access to all orders
                return;
            }
        }

    }
}
