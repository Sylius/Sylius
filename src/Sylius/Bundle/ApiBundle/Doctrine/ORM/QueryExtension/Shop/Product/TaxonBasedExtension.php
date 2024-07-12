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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ProductInterface;

final readonly class TaxonBasedExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private UserContextInterface $userContext,
    ) {
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
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

    /**
     * @param array<string>|string $taxonCode
     */
    private function addSortingToQuery(QueryBuilder $queryBuilder, array|string $taxonCode, QueryNameGeneratorInterface $queryNameGenerator): void
    {
        $taxonCode = is_array($taxonCode) ? $taxonCode : [$taxonCode];

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
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->in(sprintf('%s.code', $taxonAliasName), sprintf(':%s', $taxonCodeParameterName)),
                    $queryBuilder->expr()->eq(sprintf('%s.enabled', $taxonAliasName), 'true'),
                ),
            )
            ->orderBy(sprintf('%s.position', $productTaxonAliasName), 'ASC')
            ->setParameter($taxonCodeParameterName, $taxonCode)
        ;
    }
}
