<?php

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product\Trait;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

trait ProductLivePropTrait
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp(hydrateWith: 'hydrateProduct', dehydrateWith: 'dehydrateProduct')]
    public ?ProductInterface $product = null;

    /** @var ProductRepositoryInterface<ProductInterface> */
    protected ProductRepositoryInterface $productRepository;

    public function hydrateProduct(mixed $value): ?ProductInterface
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->find($value);

        return $product;
    }

    public function dehydrateProduct(?ProductInterface $product): mixed
    {
        return $product?->getId();
    }

    /** @param ProductRepositoryInterface<ProductInterface> $productRepository */
    protected function initializeProduct(RepositoryInterface $productRepository): void {
        $this->productRepository = $productRepository;
    }
}
