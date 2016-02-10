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
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ChannelInterface;
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
     * @param RepositoryInterface $productVariantRepository
     * @param FactoryInterface $productFactory
     * @param FactoryInterface $productVariantFactory
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $productRepository,
        RepositoryInterface $productVariantRepository,
        FactoryInterface $productFactory,
        FactoryInterface $productVariantFactory,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
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
            throw new \InvalidArgumentException(sprintf('Product with name "%s" does not exist', $productName));
        }

        return $product;
    }

    /**
     * @Transform /^"([^"]+)" variant of product "([^"]+)"$/
     */
    public function getProductVariantByNameAndProduct($variantName, $productName)
    {
        $product = $this->getProductByName($productName);

        $productVariant = $this->productVariantRepository->findOneBy(['presentation' => $variantName, 'object' => $product]);
        if (null === $productVariant) {
            throw new \InvalidArgumentException(sprintf('Product variant with name "%s" of product "%s" does not exist', $variantName, $productName));
        }

        return $productVariant;
    }

    /**
     * @Given /^the store has a product "([^"]+)"$/
     * @Given /^the store has a product "([^"]+)" priced at "(?:€|£|\$)([^"]+)"$/
     */
    public function storeHasAProductPricedAt($productName, $price = '0.00')
    {
        $product = $this->productFactory->createNew();
        $product->setName($productName);
        $product->setPrice($this->getPriceFromString($price));
        $product->setDescription('Awesome '.$productName);

        $channel = $this->sharedStorage->getCurrentResource('channel');
        $product->addChannel($channel);

        $this->productRepository->add($product);

        $this->sharedStorage->setCurrentResource('product', $product);
    }

    /**
     * @Given /^there is product "([^"]+)" available in ((?:this|that|"[^"]+") channel)$/
     */
    public function thereIsProductAvailableInGivenChannel($productName, ChannelInterface $channel)
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();
        $product->setName($productName);
        $product->setPrice(0);
        $product->setDescription('Awesome ' . $productName);
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
     * @Given /^it comes in the following variations:$/
     */
    public function itComesInTheFollowingVariations(TableNode $table)
    {
        $currentProduct = $this->sharedStorage->getCurrentResource('product');

        foreach ($table->getHash() as $variantHash) {
            $variant = $this->productVariantFactory->createNew();
            $variant->setPresentation($variantHash['name']);
            $variant->setPrice($this->getPriceFromString(str_replace(['$', '€', '£'], '', $variantHash['price'])));
            $variant->setProduct($currentProduct);

            $this->productRepository->add($variant);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^("[^"]+" variant of product "[^"]+") belongs to ("[^"]+" tax category)$/
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
