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
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

/** @experimental */
final class AddressItemExtension implements QueryItemExtensionInterface
{
    private UserContextInterface $userContext;

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
        if (!is_a($resourceClass, AddressInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();

        if ($user === null) {
            throw new MissingTokenException('JWT Token not found');
        }

        $this->applyToItemForGetMethod($user, $queryBuilder);
    }

    private function applyToItemForGetMethod(?UserInterface $user, QueryBuilder $queryBuilder): void
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

            $queryBuilder
                ->innerJoin($rootAlias.'.customer', 'customer')
                ->andWhere('customer = :customer')
                ->setParameter('customer', $customer);

            return;
        }

        throw new AccessDeniedHttpException('Requested method is not allowed.');
    }
}
