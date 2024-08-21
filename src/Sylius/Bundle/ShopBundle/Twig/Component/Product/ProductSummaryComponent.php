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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product;

use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

class ProductSummaryComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp(fieldName: 'product')]
    public Product $product;

    #[LiveProp(fieldName: 'variant')]
    public ?ProductVariant $variant = null;

    public function __construct(
        private readonly ProductVariantResolverInterface $productVariantResolver,
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        /** @var ProductVariant|null $variant * */
        $variant = $this->productVariantResolver->getVariant($this->product);

        $this->variant = $variant;
    }

    #[LiveListener('sylius:shop:variant_changed')]
    public function updateProductVariant(
        #[LiveArg] ?string $productVariantCode,
    ): void {
        $this->variant = $this->resolveProductVariant($productVariantCode);
    }

    private function resolveProductVariant(?string $productVariantCode): ?ProductVariant
    {
        if ($productVariantCode === null) {
            return null;
        }

        $variants = $this->product->getEnabledVariants();

        /** @var ProductVariant $variant */
        foreach ($variants as $variant) {
            if ($variant->getCode() === $productVariantCode) {
                return $variant;
            }
        }

        return null;
    }
}
