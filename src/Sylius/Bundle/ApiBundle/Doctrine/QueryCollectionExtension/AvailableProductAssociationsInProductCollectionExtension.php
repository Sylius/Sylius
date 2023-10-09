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

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ProductInterface;

/** @experimental */
final readonly class AvailableProductAssociationsInProductCollectionExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, ProductInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();
        if ($user !== null && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $enabledParameterName = $queryNameGenerator->generateParameterName('enabled');
        $associationAliasName = $queryNameGenerator->generateJoinAlias('association');
        $productAssociationAliasName = $queryNameGenerator->generateJoinAlias('associatedProduct');

        $queryBuilder
            ->addSelect($rootAlias)
            ->addSelect($associationAliasName)
            ->leftJoin(sprintf('%s.associations', $rootAlias), $associationAliasName)
            ->leftJoin(
                sprintf('%s.associatedProducts', $associationAliasName),
                $productAssociationAliasName,
                'WITH',
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq(sprintf('%s.enabled', $productAssociationAliasName), 'true'),
                    $queryBuilder->expr()->eq(sprintf('%s.owner', $associationAliasName), $rootAlias),
                ),
            )
            ->andWhere(sprintf('%s.associations IS EMPTY OR %s.id IS NOT NULL', $rootAlias, $productAssociationAliasName))
            ->andWhere(sprintf('%s.enabled = :%s', $rootAlias, $enabledParameterName))
            ->setParameter($enabledParameterName, true)
        ;
    }
}
