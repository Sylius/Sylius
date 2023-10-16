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

namespace Sylius\Component\Product\Resolver;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;

final class DefaultProductVariantResolver implements ProductVariantResolverInterface
{
    public function __construct(private ?ProductVariantRepositoryInterface $productVariantRepository = null)
    {
    }

    public function getVariant(ProductInterface $subject): ?ProductVariantInterface
    {
        if ($this->productVariantRepository && $subject->getId()) {
            /** @var ProductVariantInterface|null $productVariant */
            $productVariant = $this->productVariantRepository->findOneBy([
                'product' => $subject->getId(),
                'enabled' => true,
            ]);

            return $productVariant;
        }

        if ($subject->getEnabledVariants()->isEmpty()) {
            return null;
        }

        return $subject->getEnabledVariants()->first();
    }
}
