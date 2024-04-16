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
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class AddressesExtension implements ContextAwareQueryCollectionExtensionInterface
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
        if (!is_a($resourceClass, AddressInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();
        if ($user === null) {
            throw new MissingTokenException('JWT Token not found');
        }

        if ($user instanceof ShopUserInterface && in_array('ROLE_USER', $user->getRoles(), true)) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $customerParameterName = $queryNameGenerator->generateParameterName('customer');

            $queryBuilder
                ->innerJoin(sprintf('%s.customer', $rootAlias), 'customer')
                ->andWhere(sprintf('%s.customer = :%s', $rootAlias, $customerParameterName))
                ->setParameter($customerParameterName, $user->getCustomer())
            ;

            return;
        }

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        throw new AccessDeniedHttpException('Requested method is not allowed.');
    }
}
