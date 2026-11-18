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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Webmozart\Assert\Assert;

final class InForTaxonsScopeVariantCodesProvider implements VariantCodesInScopeProviderInterface
{
    /**
     * @param class-string $productVariantClass
     * @param class-string $productTaxonClass
     * @param class-string $taxonClass
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $productVariantClass,
        private readonly string $productTaxonClass,
        private readonly string $taxonClass,
    ) {
    }

    public function provide(CatalogPromotionScopeInterface $scope): array
    {
        $configuration = $scope->getConfiguration();
        Assert::keyExists($configuration, 'taxons', 'This rule should have configured taxons');

        if ($configuration['taxons'] === []) {
            return [];
        }

        // Covers the configured taxons and all their descendants (same nested set tree, enclosed bounds).
        // A configured taxon that does not exist matches nothing, it is up to the checker to report it.
        return array_values($this->entityManager
            ->createQuery(sprintf(
                'SELECT variant.code
                FROM %s variant
                JOIN variant.product product
                WHERE EXISTS (
                    SELECT productTaxon.id
                    FROM %s productTaxon
                    JOIN productTaxon.taxon scopedTaxon, %s configuredTaxon
                    WHERE productTaxon.product = product
                        AND configuredTaxon.code IN (:taxonCodes)
                        AND (
                            scopedTaxon.id = configuredTaxon.id
                            OR (
                                scopedTaxon.root = configuredTaxon.root
                                AND scopedTaxon.left >= configuredTaxon.left
                                AND scopedTaxon.right <= configuredTaxon.right
                            )
                        )
                )',
                $this->productVariantClass,
                $this->productTaxonClass,
                $this->taxonClass,
            ))
            ->setParameter('taxonCodes', $configuration['taxons'])
            ->getSingleColumnResult())
        ;
    }
}
