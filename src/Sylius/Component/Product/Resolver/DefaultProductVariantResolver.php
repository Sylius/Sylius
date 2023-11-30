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
        if (null === $productVariantRepository) {
            trigger_deprecation(
                'sylius/product',
                '1.12',
                'Not passing a service that implements "%s" as a 1st argument of "%s" constructor is deprecated and will be prohibited in 2.0.',
                ProductVariantRepositoryInterface::class,
                self::class,
            );
        }
    }

    public function getVariant(ProductInterface $subject): ?ProductVariantInterface
    {
        if ($this->productVariantRepository && $subject->getId()) {
            /** @var ProductVariantInterface[] $productVariants */
            $productVariants = $this->productVariantRepository->findBy(
                [
                    'product' => $subject,
                    'enabled' => true,
                ],
                [
                    'position' => 'ASC',
                    'id' => 'ASC',
                ],
                1,
            );

            return $productVariants[0] ?? null;
        }

        if ($subject->getEnabledVariants()->isEmpty()) {
            return null;
        }

        return $subject->getEnabledVariants()->first();
    }
}
