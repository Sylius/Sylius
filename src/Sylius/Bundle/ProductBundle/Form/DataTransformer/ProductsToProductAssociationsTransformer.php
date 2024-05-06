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

namespace Sylius\Bundle\ProductBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

final class ProductsToProductAssociationsTransformer implements DataTransformerInterface
{
    /** @var Collection<array-key, ProductAssociationInterface>|null */
    private ?Collection $productAssociations = null;

    /**
     * @param FactoryInterface<ProductAssociationInterface> $productAssociationFactory
     * @param RepositoryInterface<ProductAssociationTypeInterface> $productAssociationTypeRepository
     */
    public function __construct(
        private readonly FactoryInterface $productAssociationFactory,
        private readonly ?ProductRepositoryInterface $productRepository,
        private readonly RepositoryInterface $productAssociationTypeRepository,
    ) {
        if (null !== $productRepository) {
            trigger_deprecation(
                'sylius/product-bundle',
                '1.14',
                'Passing the second argument "%s" of the constructor is deprecated and will be removed in Sylius 2.0.',
                ProductRepositoryInterface::class,
            );
        }
    }

    /**
     * @return array<string, Collection<array-key, ProductInterface>>|string
     */
    public function transform(mixed $value): array|string
    {
        $this->setProductAssociations($value);

        if ($this->productRepository !== null) {
            if (null === $value) {
                return '';
            }
        } else {
            if ($value->isEmpty()) {
                return [];
            }
        }

        $values = [];

        /** @var ProductAssociationInterface $productAssociation */
        foreach ($value as $productAssociation) {
            if ($this->productRepository !== null) {
                $productCodesAsString = $this->getCodesAsStringFromProducts($productAssociation->getAssociatedProducts());

                $values[$productAssociation->getType()->getCode()] = $productCodesAsString;
            } else {
                $values[$productAssociation->getType()->getCode()] = clone $productAssociation->getAssociatedProducts();
            }
        }

        return $values;
    }

    public function reverseTransform(mixed $value): ?Collection
    {
        if (null === $value || '' === $value || !is_array($value)) {
            return null;
        }

        /** @var Collection<array-key, ProductAssociationInterface> $productAssociations */
        $productAssociations = new ArrayCollection();
        if ($this->productRepository !== null) {
            foreach ($value as $productAssociationTypeCode => $productCodes) {
                if (null === $productCodes) {
                    continue;
                }

                /** @var ProductAssociationInterface $productAssociation */
                $productAssociation = $this->getProductAssociationByTypeCode((string) $productAssociationTypeCode);
                $this->setAssociatedProductsByProductCodes($productAssociation, $productCodes);
                $productAssociations->add($productAssociation);
            }
        } else {
            foreach ($value as $productAssociationTypeCode => $products) {
                if (null === $products || [] === $products) {
                    continue;
                }

                $productAssociation = $this->getProductAssociationByTypeCode((string) $productAssociationTypeCode);
                $this->linkProductsToAssociation($productAssociation, $products);
                $productAssociations->add($productAssociation);
            }
        }

        $this->setProductAssociations(null);

        return $productAssociations;
    }

    /**
     * @param Collection<array-key, ProductInterface> $products
     */
    private function getCodesAsStringFromProducts(Collection $products): ?string
    {
        if ($products->isEmpty()) {
            return null;
        }

        $codes = [];

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $codes[] = $product->getCode();
        }

        return implode(',', $codes);
    }

    private function getProductAssociationByTypeCode(string $productAssociationTypeCode): ProductAssociationInterface
    {
        foreach ($this->productAssociations as $productAssociation) {
            if ($productAssociationTypeCode === $productAssociation->getType()->getCode()) {
                return $productAssociation;
            }
        }

        /** @var ProductAssociationTypeInterface $productAssociationType */
        $productAssociationType = $this->productAssociationTypeRepository->findOneBy([
            'code' => $productAssociationTypeCode,
        ]);

        /** @var ProductAssociationInterface $productAssociation */
        $productAssociation = $this->productAssociationFactory->createNew();
        $productAssociation->setType($productAssociationType);

        return $productAssociation;
    }

    private function setAssociatedProductsByProductCodes(
        ProductAssociationInterface $productAssociation,
        string $productCodes,
    ): void {
        $products = $this->productRepository->findBy(['code' => explode(',', $productCodes)]);

        $productAssociation->clearAssociatedProducts();
        foreach ($products as $product) {
            Assert::isInstanceOf($product, ProductInterface::class);
            $productAssociation->addAssociatedProduct($product);
        }
    }

    /**
     * @param Collection<array-key, ProductInterface> $products
     */
    private function linkProductsToAssociation(
        ProductAssociationInterface $productAssociation,
        Collection $products,
    ): void {
        $productAssociation->clearAssociatedProducts();
        foreach ($products as $product) {
            Assert::isInstanceOf($product, ProductInterface::class);
            $productAssociation->addAssociatedProduct($product);
        }
    }

    private function setProductAssociations(?Collection $productAssociations): void
    {
        $this->productAssociations = $productAssociations instanceof Collection ? $productAssociations : new ArrayCollection();
    }
}
