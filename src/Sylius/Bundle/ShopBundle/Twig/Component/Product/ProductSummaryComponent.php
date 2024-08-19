<?php

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product;

use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

final class ProductSummaryComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp(fieldName: 'product')]
    public Product $product;

    #[LiveProp(fieldName: 'variant')]
    public ?ProductVariant $variant = null;

    public function __construct(
        private readonly ProductVariantResolverInterface $productVariantResolver
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        /** @var ProductVariant|null $variant **/
        $variant = $this->productVariantResolver->getVariant($this->product);

        $this->variant = $variant;
    }

    #[LiveListener('variantChanged')]
    public function updateProductVariant(
        #[LiveArg] string $productVariantCode,
    ): void {
        $this->variant = $this->resolveProductVariant($productVariantCode);
    }

    private function resolveProductVariant(string $productVariantCode): ProductVariant
    {
        $variants = $this->product->getEnabledVariants();

        if ($this->product->getVariantSelectionMethod() === ProductInterface::VARIANT_SELECTION_MATCH) {
            /** @var ProductVariant $variant */
            foreach ($variants as $variant) {
                $values = $variant->getOptionValues();

                foreach ($values as $value) {
                    if ($value->getCode() === $productVariantCode) {
                        return $variant;
                    }
                }

            }
        } else {
            /** @var ProductVariant $variant */
            foreach ($variants as $variant) {
                if ($variant->getCode() === $productVariantCode) {
                    return $variant;
                }
            }
        }

        throw new \InvalidArgumentException('Product variant with code "' . $productVariantCode . '" not found.');
    }
}
