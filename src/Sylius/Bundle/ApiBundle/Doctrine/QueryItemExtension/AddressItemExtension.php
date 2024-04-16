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
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

final class AddressItemExtension implements QueryItemExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?string $operationName = null,
        array $context = [],
    ) {
        if (!is_a($resourceClass, AddressInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();

        if ($user === null) {
            throw new MissingTokenException('JWT Token not found');
        }

        $this->applyToItemForGetMethod($user, $queryBuilder, $queryNameGenerator);
    }

    private function applyToItemForGetMethod(?UserInterface $user, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator): void
    {
        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        /** @var CustomerInterface|null $customer */
        $customer = $user instanceof ShopUserInterface ? $user->getCustomer() : null;

        if (
            $user instanceof ShopUserInterface &&
            $customer !== null &&
            in_array('ROLE_USER', $user->getRoles(), true)
        ) {
            $rootAlias = $queryBuilder->getRootAliases()[0];

            $customerParameterName = $queryNameGenerator->generateParameterName('customer');

            $queryBuilder
                ->innerJoin($rootAlias . '.customer', 'customer')
                ->andWhere(sprintf('customer = :%s', $customerParameterName))
                ->setParameter($customerParameterName, $customer)
            ;

            return;
        }

        throw new AccessDeniedHttpException('Requested method is not allowed.');
    }
}
