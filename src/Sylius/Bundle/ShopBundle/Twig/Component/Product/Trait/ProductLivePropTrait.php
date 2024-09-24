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

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait ProductLivePropTrait
{
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
    protected function initializeProduct(RepositoryInterface $productRepository): void
    {
        $this->productRepository = $productRepository;
    }
}
