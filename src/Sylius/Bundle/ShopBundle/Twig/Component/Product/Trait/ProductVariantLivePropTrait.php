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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait ProductVariantLivePropTrait
{
    #[LiveProp(hydrateWith: 'hydrateProductVariant', dehydrateWith: 'dehydrateProductVariant')]
    public ?ProductVariantInterface $variant = null;

    /** @var ProductVariantRepositoryInterface<ProductVariantInterface> */
    protected ProductVariantRepositoryInterface $productVariantRepository;

    public function hydrateProductVariant(mixed $value): ?ProductVariantInterface
    {
        if (empty($value)) {
            return null;
        }

        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantRepository->find($value);

        return $variant;
    }

    public function dehydrateProductVariant(?ProductVariantInterface $product): mixed
    {
        return $product?->getId();
    }

    /** @param ProductVariantRepositoryInterface<ProductVariantInterface> $productVariantRepository*/
    protected function initializeProductVariant(ProductVariantRepositoryInterface $productVariantRepository): void
    {
        $this->productVariantRepository = $productVariantRepository;
    }
}
