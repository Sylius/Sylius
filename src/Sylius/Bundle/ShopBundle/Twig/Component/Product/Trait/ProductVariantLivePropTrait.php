<?php

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

trait ProductVariantLivePropTrait
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp(hydrateWith: "hydrateProductVariant", dehydrateWith: "dehydrateProductVariant")]
    public ?ProductVariantInterface $variant = null;

    /** @var ProductVariantRepositoryInterface<ProductVariantInterface> */
    protected ProductVariantRepositoryInterface $productVariantRepository;

    public function hydrateProductVariant(mixed $value): ?ProductVariantInterface
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productRepository->find($value);

        return $variant;
    }

    public function dehydrateProductVariant(?ProductVariantInterface $product): mixed
    {
        return $product?->getId();
    }

    /** @param ProductVariantRepositoryInterface<ProductVariantInterface> $productVariantRepository*/
    protected function initializeProductVariant(
        ProductVariantRepositoryInterface $productVariantRepository,
    ): void {
        $this->productVariantRepository = $productVariantRepository;
    }
}
