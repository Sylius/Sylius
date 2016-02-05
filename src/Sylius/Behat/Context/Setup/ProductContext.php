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
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @var RepositoryInterface
     */
    private $taxCategoryRepository;

    /**
     * @var RepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @var FactoryInterface
     */
    private $productFactory;

    /**
     * @var FactoryInterface
     */
    private $productVariantFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $productRepository
     * @param RepositoryInterface $taxCategoryRepository
     * @param RepositoryInterface $productVariantRepository
     * @param FactoryInterface $productFactory
     * @param FactoryInterface $productVariantFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $productRepository,
        RepositoryInterface $taxCategoryRepository,
        RepositoryInterface $productVariantRepository,
        FactoryInterface $productFactory,
        FactoryInterface $productVariantFactory,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->productFactory = $productFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @Transform /^product "([^"]+)"$/
     * @Transform /^"([^"]+)" product$/
     * @Transform :product
     */
    public function getProductByName($productName)
    {
        $product = $this->productRepository->findOneBy(['name' => $productName]);
        if (null === $product) {
            throw new \InvalidArgumentException('Product with name "'.$productName.'" does not exist');
        }

        return $product;
    }

    /**
     * @Transform :variantName product variant
     * @Transform product variant :variantName
     * @Transform :variantName
     */
    public function getProductVariantByName($variantName)
    {
        $productVariant = $this->productVariantRepository->findOneBy(array('presentation' => $variantName));
        if (null === $productVariant) {
            throw new \InvalidArgumentException('Product variant with name "'.$variantName.'" does not exist');
        }

        return $productVariant;
    }

    /**
     * @Given /^store has a product "([^"]+)" priced at "(?:€|£|\$)([^"]+)"$/
     */
    public function storeHasAProductPricedAt($productName, $price)
    {
        $product = $this->productFactory->createNew();
        $product->setName($productName);
        $product->setPrice($this->getPriceFromString($price));
        $product->setDescription('Awesome '.$productName);

        $channel = $this->sharedStorage->getCurrentResource('channel');
        $product->addChannel($channel);

        $this->productRepository->add($product);
    }

    /**
     * @Given /^(product "[^"]+") belongs to ("[^"]+" tax category)$/
     */
    public function productBelongsToTaxCategory(ProductInterface $product, TaxCategoryInterface $taxCategory)
    {
        $product->getMasterVariant()->setTaxCategory($taxCategory);
        $this->objectManager->flush();
    }

    /**
     * @Given /^store has a product "([^"]+)" with "([^"]+)" variant priced at "(?:€|£|\$)([^"]+)" and "([^"]+)" variant priced at "(?:€|£|\$)([^"]+)"$/
     */
    public function storeHasAProductWithAndVariants($productName, $firstVariantName, $firstVariantPrice, $secondVariantName, $secondVariantPrice)
    {
        $product = $this->productFactory->createNew();
        $product->setName($productName);
        $product->setDescription('Awesome '.$productName);

        $channel = $this->sharedStorage->getCurrentResource('channel');
        $product->addChannel($channel);

        /** @var ProductVariantInterface $firstVariant */
        $firstVariant = $this->productVariantFactory->createNew();
        $firstVariant->setPresentation($firstVariantName);
        $firstVariant->setPrice((int) $firstVariantPrice * 100);
        $firstVariant->setProduct($product);

        $product->setPrice($firstVariant->getPrice());

        /** @var ProductVariantInterface $firstVariant */
        $secondVariant = $this->productVariantFactory->createNew();
        $secondVariant->setPresentation($secondVariantName);
        $secondVariant->setPrice((int) $secondVariantPrice * 100);
        $secondVariant->setProduct($product);

        $this->productRepository->add($product);
        $this->productVariantRepository->add($firstVariant);
        $this->productVariantRepository->add($secondVariant);
    }

    /**
     * @Given /^(product variant "[^"]+") belongs to ("[^"]+" tax category)$/
     */
    public function productVariantBelongsToTaxCategory(ProductVariantInterface $productVariant, TaxCategoryInterface $taxCategory)
    {
        $productVariant->setTaxCategory($taxCategory);
        $this->objectManager->flush($productVariant);
    }

    /**
     * @param string $price
     *
     * @return int
     */
    private function getPriceFromString($price)
    {
        return (int) round(($price * 100), 2);
    }
}
