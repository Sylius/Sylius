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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ProductContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $productRepository;

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
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $productFactory
     */
    public function __construct(RepositoryInterface $productRepository, SharedStorageInterface $sharedStorage, FactoryInterface $productFactory)
    {
        $this->productRepository = $productRepository;
        $this->sharedStorage = $sharedStorage;
        $this->productFactory = $productFactory;
    }

    /**
     * @Given /^store has a product "([^"]*)" priced at "(€|£|\$)([^"]*)"$/
     */
    public function storeHasAProductPricedAt($productName, $currency, $price)
    {
        $product = $this->productFactory->createNew();
        $product->setName($productName);
        $product->setPrice((int) $price * 100);
        $product->setDescription('Awesome star wars mug');

        $channel = $this->sharedStorage->getCurrentResource('channel');
        $product->addChannel($channel);

        $this->productRepository->add($product);
    }
}
