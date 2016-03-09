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
use Sylius\Behat\Page\Admin\Product\IndexPageInterface;
use Sylius\Behat\Page\Admin\Product\ShowPageInterface;
use Sylius\Behat\Page\Product\ProductShowPage;
use Sylius\Behat\Page\Admin\Product\ShowPage as AdminProductShowPage;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ProductShowPage
     */
    private $productShowPage;

    /**
     * @var AdminProductShowPage
     */
    private $adminProductShowPage;

    /**
     * @var IndexPageInterface
     */
    private $productIndexPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ProductShowPage $productShowPage
     * @param ShowPageInterface $adminProductShowPage
     * @param IndexPageInterface $productIndexPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductShowPage $productShowPage,
        ShowPageInterface $adminProductShowPage,
        IndexPageInterface $productIndexPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productShowPage = $productShowPage;
        $this->adminProductShowPage = $adminProductShowPage;
        $this->productIndexPage = $productIndexPage;
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
    public function iDeleteProduct(ProductInterface $product)
    {
        $this->sharedStorage->set('product', $product);

        $this->adminProductShowPage->open(['id' => $product->getId()]);

        $this->adminProductShowPage->deleteProduct();
    }

    /**
     * @Then /^(this product) should not exist in the product catalog$/
     */
    public function productShouldNotExist(ProductInterface $product)
    {
        $this->productIndexPage->open();

        expect($this->productIndexPage->isThereProduct($product))->toBe(false);
    }
}
