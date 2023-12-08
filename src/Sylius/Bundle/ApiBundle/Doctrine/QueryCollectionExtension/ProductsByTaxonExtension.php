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
final class ProductsByTaxonExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public function __construct(
        private UserContextInterface $userContext,
    ) {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, ProductInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();
        if ($user !== null && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        $taxonCode = $context['filters']['productTaxons.taxon.code'] ?? null;
        if (null === $taxonCode) {
            return;
        }

        $this->addSortingToQuery($queryBuilder, $taxonCode, $queryNameGenerator);
    }

    private function addSortingToQuery(QueryBuilder $queryBuilder, string $taxonCode, QueryNameGeneratorInterface $queryNameGenerator): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $taxonCodeParameterName = $queryNameGenerator->generateParameterName('taxonCode');
        $productTaxonAliasName = $queryNameGenerator->generateJoinAlias('productTaxons');
        $taxonAliasName = $queryNameGenerator->generateJoinAlias('taxon');

        $queryBuilder
            ->addSelect($productTaxonAliasName)
            ->leftJoin(
                sprintf('%s.productTaxons', $rootAlias),
                $productTaxonAliasName,
                'WITH',
                sprintf('%s.product = %s.id', $productTaxonAliasName, $rootAlias),
            )
            ->leftJoin(
                sprintf('%s.taxon', $productTaxonAliasName),
                $taxonAliasName,
                'WITH',
                sprintf('%s.code = :%s', $taxonAliasName, $taxonCodeParameterName),
            )
            ->orderBy(sprintf('%s.position', $productTaxonAliasName), 'ASC')
            ->setParameter($taxonCodeParameterName, $taxonCode)
        ;
    }
}
