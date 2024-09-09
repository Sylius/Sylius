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

namespace Sylius\Bundle\AdminBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

/**
 * @implements DataTransformerInterface<Collection<array-key, ProductAssociationInterface>, array<string, Collection<array-key, ProductInterface>>>
 */
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
        private readonly RepositoryInterface $productAssociationTypeRepository,
    ) {
    }

    /**
     * @return array<string, Collection<array-key, ProductInterface>>
     */
    public function transform(mixed $value): array
    {
        $this->setProductAssociations($value);

        if ($value->isEmpty()) {
            return [];
        }

        $values = [];

        /** @var ProductAssociationInterface $productAssociation */
        foreach ($value as $productAssociation) {
            $values[$productAssociation->getType()->getCode()] = clone $productAssociation->getAssociatedProducts();
        }

        return $values;
    }

    public function reverseTransform(mixed $value): ?Collection
    {
        if (!is_array($value)) {
            return null;
        }

        /** @var Collection<array-key, ProductAssociationInterface> $productAssociations */
        $productAssociations = new ArrayCollection();
        foreach ($value as $productAssociationTypeCode => $products) {
            if ($products->isEmpty()) {
                continue;
            }

            $productAssociation = $this->getProductAssociationByTypeCode((string) $productAssociationTypeCode);
            $this->linkProductsToAssociation($productAssociation, $products);
            $productAssociations->add($productAssociation);
        }

        $this->setProductAssociations(null);

        return $productAssociations;
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

    /**
     * @param Collection<array-key, ProductAssociationInterface>|null $productAssociations
     */
    private function setProductAssociations(?Collection $productAssociations): void
    {
        $this->productAssociations = $productAssociations instanceof Collection ? $productAssociations : new ArrayCollection();
    }
}
