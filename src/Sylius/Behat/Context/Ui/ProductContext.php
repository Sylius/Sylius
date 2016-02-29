<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Product\ProductShowPage;
use Sylius\Behat\Page\Admin\Product\ProductShowPage as AdminProductShowPage;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var ProductShowPageInterface
     */
    private $productShowPage;

    /**
     * @var AdminProductShowPage
     */
    private $adminProductShowPage;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ProductShowPage $productShowPage
     * @param AdminProductShowPage $adminProductShowPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductShowPage $productShowPage,
        AdminProductShowPage $adminProductShowPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productShowPage = $productShowPage;
        $this->adminProductShowPage = $adminProductShowPage;
    }

    /**
     * @Then I should be able to access product :product
     */
    public function iShouldBeAbleToAccessProduct(ProductInterface $product)
    {
        $this->productShowPage->tryToOpen(['product' => $product]);

        expect($this->productShowPage->isOpen(['product' => $product]))->toBe(true);
    }

    /**
     * @Then I should not be able to access product :product
     */
    public function iShouldNotBeAbleToAccessProduct(ProductInterface $product)
    {
        $this->productShowPage->tryToOpen(['product' => $product]);

        expect($this->productShowPage->isOpen(['product' => $product]))->toBe(false);
    }

    /**
     * @When I delete the :product product
     */
    public function iDeleteProduct($productName)
    {
        $product = $this->sharedStorage->get('product');

        if ($product->getName() !== $productName) {
            throw new \InvalidArgumentException('There is no such product in the store right now!');
        }

        $this->adminProductShowPage->open(['id' => $product->getId()]);

        $this->adminProductShowPage->deleteProduct();
    }

    /**
     * @Then this product should not exist in the product catalog
     */
    public function productShouldNotExist()
    {
        $product = $this->sharedStorage->get('product');

        $this->adminProductShowPage->tryToOpen(['id' => $product->getId()]);

        expect($this->adminProductShowPage->isOpen(['id' => $product->getId()]))->toBe(false);
    }
}
