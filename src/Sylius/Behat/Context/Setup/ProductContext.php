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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @var RepositoryInterface
     */
    private $taxCategoryRepository;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $productFactory;

    /**
     * @param RepositoryInterface $productRepository
     * @param RepositoryInterface $taxCategoryRepository
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $productFactory
     */
    public function __construct(
        RepositoryInterface $productRepository,
        RepositoryInterface $taxCategoryRepository,
        SharedStorageInterface $sharedStorage,
        FactoryInterface $productFactory
    ) {
        $this->productRepository = $productRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->sharedStorage = $sharedStorage;
        $this->productFactory = $productFactory;
    }

    /**
     * @Transform /^product "([^"]+)"$/
     * @Transform /^"([^"]+)" product$/
     */
    public function castProductNameToProduct($productName)
    {
        if (null === $product = $this->productRepository->findOneBy(array('name' => $productName))) {
            throw new \Exception('Product with name "'.$product.'" does not exist');
        }

        return $product;
    }

    /**
     * @Given /^store has a product "([^"]+)" priced at "(€|£|\$)([^"]+)"$/
     */
    public function storeHasAProductPricedAt($productName, $currency, $price)
    {
        $product = $this->productFactory->createNew();
        $product->setName($productName);
        $product->setPrice((int) $price * 100);
        $product->setDescription('Awesome '.$productName);

        $channel = $this->sharedStorage->getCurrentResource('channel');
        $product->addChannel($channel);

        $this->productRepository->add($product);
    }

    /**
     * @Given /^(product "[^"]+") belongs to ("[^"]+" tax category)$/
     */
    public function productBelongsToTaxCategory(ProductInterface $product, $taxCategory)
    {
        $product->setTaxCategory($taxCategory);
    }
}
