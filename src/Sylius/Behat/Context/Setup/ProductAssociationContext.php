<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductAssociationContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $productAssociationTypeFactory;

    /**
     * @var FactoryInterface
     */
    private $productAssociationFactory;

    /**
     * @var RepositoryInterface
     */
    private $productAssociationTypeRepository;

    /**
     * @var RepositoryInterface
     */
    private $productAssociationRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $productAssociationTypeFactory
     * @param FactoryInterface $productAssociationFactory
     * @param RepositoryInterface $productAssociationTypeRepository
     * @param RepositoryInterface $productAssociationRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $productAssociationTypeFactory,
        FactoryInterface $productAssociationFactory,
        RepositoryInterface $productAssociationTypeRepository,
        RepositoryInterface $productAssociationRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productAssociationTypeFactory = $productAssociationTypeFactory;
        $this->productAssociationFactory = $productAssociationFactory;
        $this->productAssociationTypeRepository = $productAssociationTypeRepository;
        $this->productAssociationRepository = $productAssociationRepository;
    }

    /**
     * @Given the store has (also) a product association type :name
     * @Given the store has (also) a product association type :name with a code :code
     */
    public function theStoreHasAProductAssociationType($name, $code = null)
    {
        $this->createProductAssociationType($name, $code);
    }

    /**
     * @Given /^the (product "[^"]+") has(?:| also) an (association "[^"]+") with (products "[^"]+" and "[^"]+")$/
     */
    public function theProductHasAnAssociationWithProducts(
        ProductInterface $product,
        ProductAssociationTypeInterface $productAssociationType,
        array $products
    ) {
        $this->createProductAssociation($product, $productAssociationType, $products);
    }

    /**
     * @param string $name
     * @param string|null $code
     */
    private function createProductAssociationType($name, $code = null)
    {
        if (null === $code) {
            $code = $this->generateCodeFromName($name);
        }

        /** @var ProductAssociationTypeInterface $productAssociationType */
        $productAssociationType = $this->productAssociationTypeFactory->createNew();
        $productAssociationType->setCode($code);
        $productAssociationType->setName($name);

        $this->productAssociationTypeRepository->add($productAssociationType);
        $this->sharedStorage->set('product_association_type', $productAssociationType);
    }

    /**
     * @param ProductInterface $product
     * @param ProductAssociationTypeInterface $productAssociationType
     * @param array $associatedProducts
     */
    private function createProductAssociation(
        ProductInterface $product,
        ProductAssociationTypeInterface $productAssociationType,
        array $associatedProducts
    ) {
        /** @var ProductAssociationInterface $productAssociation */
        $productAssociation = $this->productAssociationFactory->createNew();
        $productAssociation->setType($productAssociationType);

        foreach ($associatedProducts as $associatedProduct) {
            $productAssociation->addAssociatedProduct($associatedProduct);
        }

        $product->addAssociation($productAssociation);

        $this->productAssociationRepository->add($productAssociation);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function generateCodeFromName($name)
    {
        return str_replace([' ', '-'], '_', strtolower($name));
    }
}
