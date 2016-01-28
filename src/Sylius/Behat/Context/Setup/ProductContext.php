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
     * @var FactoryInterface
     */
    private $productFactory;

    /**
     * @var ObjectManager
     */
    private $productManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $productRepository
     * @param RepositoryInterface $taxCategoryRepository
     * @param FactoryInterface $productFactory
     * @param ObjectManager $productManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $productRepository,
        RepositoryInterface $taxCategoryRepository,
        FactoryInterface $productFactory,
        ObjectManager $productManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;
        $this->productFactory = $productFactory;
        $this->productManager = $productManager;
    }

    /**
     * @Transform /^product "([^"]+)"$/
     * @Transform /^"([^"]+)" product$/
     */
    public function castProductNameToProduct($productName)
    {
        $product = $this->productRepository->findOneBy(['name' => $productName]);
        if (null === $product) {
            throw new \InvalidArgumentException('Product with name "'.$productName.'" does not exist');
        }

        return $product;
    }

    /**
     * @Given /^store has a product "([^"]+)" priced at "(?:€|£|\$)([^"]+)"$/
     */
    public function storeHasAProductPricedAt($productName, $price)
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
    public function productBelongsToTaxCategory(ProductInterface $product, TaxCategoryInterface $taxCategory)
    {
        $product->setTaxCategory($taxCategory);
        $this->productManager->flush($product);
    }
}
