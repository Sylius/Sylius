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
use Sylius\Component\Association\Model\AssociationTypeInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
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
            return null;
        }

        $values = [];

        /** @var ProductAssociationInterface $productAssociation */
        foreach ($productAssociations as $productAssociation) {
            $productIdsAsString = $this->getIdsAsStringFromProducts($productAssociation->getAssociatedObjects());

            $values[$productAssociation->getType()->getCode()] = $productIdsAsString;
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
        foreach ($values as $productAssociationTypeCode => $productIds) {
            if (null === $productIds) {
                continue;
            }

            /** @var ProductAssociationInterface $productAssociation */
            $productAssociation = $this->getProductAssociationByTypeCode($productAssociationTypeCode);
            $this->setAssociatedObjectsByProductIds($productAssociation, $productIds);
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
    private function getIdsAsStringFromProducts(Collection $products)
    {
        if ($products->isEmpty()) {
            return null;
        }

        $ids = [];

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $ids[] = $product->getId();
        }

        return implode(',', $ids);
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

        /** @var AssociationTypeInterface $productAssociationType */
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
     * @param string $productIds
     */
    private function setAssociatedObjectsByProductIds(ProductAssociationInterface $productAssociation, $productIds)
    {
        $products = $this->productRepository->findBy(['id' => explode(',', $productIds)]);

        $productAssociation->clearAssociatedObjects();
        foreach ($products as $product) {
            $productAssociation->addAssociatedObject($product);
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
