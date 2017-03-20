<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductsToProductAssociationsTransformer implements DataTransformerInterface
{
    /**
     * @var FactoryInterface
     */
    protected $productAssociationFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var RepositoryInterface
     */
    protected $productAssociationTypeRepository;

    /**
     * @var Collection
     */
    private $productAssociations;

    /**
     * @param FactoryInterface $productAssociationFactory
     * @param ProductRepositoryInterface $productRepository
     * @param RepositoryInterface $productAssociationTypeRepository
     */
    public function __construct(
        FactoryInterface $productAssociationFactory,
        ProductRepositoryInterface $productRepository,
        RepositoryInterface $productAssociationTypeRepository
    ) {
        $this->productAssociationFactory = $productAssociationFactory;
        $this->productRepository = $productRepository;
        $this->productAssociationTypeRepository = $productAssociationTypeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($productAssociations)
    {
        $this->setProductAssociations($productAssociations);

        if (null === $productAssociations) {
            return '';
        }

        $values = [];

        /** @var ProductAssociationInterface $productAssociation */
        foreach ($productAssociations as $productAssociation) {
            $productCodesAsString = $this->getCodesAsStringFromProducts($productAssociation->getAssociatedProducts());

            $values[$productAssociation->getType()->getCode()] = $productCodesAsString;
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($values)
    {
        if (null === $values || '' === $values || !is_array($values)) {
            return null;
        }

        $productAssociations = new ArrayCollection();
        foreach ($values as $productAssociationTypeCode => $productCodes) {
            if (null === $productCodes) {
                continue;
            }

            /** @var ProductAssociationInterface $productAssociation */
            $productAssociation = $this->getProductAssociationByTypeCode($productAssociationTypeCode);
            $this->setAssociatedProductsByProductCodes($productAssociation, $productCodes);
            $productAssociations->add($productAssociation);
        }

        $this->setProductAssociations(null);

        return $productAssociations;
    }

    /**
     * @param Collection $products
     *
     * @return string
     */
    private function getCodesAsStringFromProducts(Collection $products)
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

    /**
     * @param string $productAssociationTypeCode
     *
     * @return ProductAssociationInterface
     */
    private function getProductAssociationByTypeCode($productAssociationTypeCode)
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
     * @param ProductAssociationInterface $productAssociation
     * @param string $productCodes
     */
    private function setAssociatedProductsByProductCodes(ProductAssociationInterface $productAssociation, $productCodes)
    {
        $products = $this->productRepository->findBy(['code' => explode(',', $productCodes)]);

        $productAssociation->clearAssociatedProducts();
        foreach ($products as $product) {
            $productAssociation->addAssociatedProduct($product);
        }
    }

    /**
     * @param Collection|null $productAssociations
     */
    private function setProductAssociations($productAssociations)
    {
        $this->productAssociations = $productAssociations;
    }
}
