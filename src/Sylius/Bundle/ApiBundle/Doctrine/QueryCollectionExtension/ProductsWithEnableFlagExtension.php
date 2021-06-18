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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ProductInterface;

/** @experimental */
final class ProductsWithEnableFlagExtension implements ContextAwareQueryCollectionExtensionInterface
{
    /** @var UserContextInterface */
    private $userContext;

    public function __construct(UserContextInterface $userContext)
    {
        $this->userContext = $userContext;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
        array $context = []
    ): void {
        if (!is_a($resourceClass, ProductInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();
        if ($user !== null && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere($rootAlias . '.enabled = :enabled');
        $queryBuilder->setParameter('enabled', true);
    }
}
