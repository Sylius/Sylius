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

namespace Sylius\Bundle\AdminBundle\Twig\Component\ProductVariant;

use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
class FormComponent
{
    /** @use ResourceFormComponentTrait<ProductVariantInterface> */
    use ResourceFormComponentTrait;

    use TemplatePropTrait;

    #[LiveProp(hydrateWith: 'hydrateProduct', dehydrateWith: 'dehydrateProduct', fieldName: 'product')]
    public ProductInterface $product;

    /**
     * @param RepositoryInterface<ProductVariantInterface> $productVariantRepository
     * @param ProductVariantFactoryInterface<ProductVariantInterface> $productVariantFactory
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     */
    public function __construct(
        RepositoryInterface $productVariantRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        protected readonly ProductVariantFactoryInterface $productVariantFactory,
        protected readonly ProductRepositoryInterface $productRepository,
    ) {
        $this->initialize($productVariantRepository, $formFactory, $resourceClass, $formClass);
    }

    public function hydrateProduct(mixed $value): ?ProductInterface
    {
        return $this->productRepository->find($value);
    }

    public function dehydrateProduct(ProductInterface $product): mixed
    {
        return $product->getId();
    }

    /** @return ProductVariantInterface */
    protected function createResource(): ResourceInterface
    {
        return $this->productVariantFactory->createForProduct($this->product);
    }
}
