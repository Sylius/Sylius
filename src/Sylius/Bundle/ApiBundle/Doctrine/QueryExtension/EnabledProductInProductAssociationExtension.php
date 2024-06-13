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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryExtension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;

final readonly class EnabledProductInProductAssociationExtension implements QueryItemExtensionInterface, QueryCollectionExtensionInterface
{
    public function __construct(private SectionProviderInterface $sectionProvider)
    {
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->modifyQueryBuilder($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->modifyQueryBuilder($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    private function modifyQueryBuilder(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
    ): void {
        if (!is_a($resourceClass, ProductInterface::class, true)) {
            return;
        }

        if (!$this->sectionProvider->getSection() instanceof ShopApiSection) {
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
