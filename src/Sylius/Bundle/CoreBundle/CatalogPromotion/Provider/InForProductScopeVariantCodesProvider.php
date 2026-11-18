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

final class InForProductScopeVariantCodesProvider implements VariantCodesInScopeProviderInterface
{
    /** @param class-string $productVariantClass */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $productVariantClass,
    ) {
    }

    public function provide(CatalogPromotionScopeInterface $scope): array
    {
        $configuration = $scope->getConfiguration();
        Assert::keyExists($configuration, 'products', 'This rule should have configured products');

        if ($configuration['products'] === []) {
            return [];
        }

        return array_values($this->entityManager
            ->createQuery(sprintf(
                'SELECT variant.code FROM %s variant JOIN variant.product product WHERE product.code IN (:productCodes)',
                $this->productVariantClass,
            ))
            ->setParameter('productCodes', $configuration['products'])
            ->getSingleColumnResult())
        ;
    }
}
