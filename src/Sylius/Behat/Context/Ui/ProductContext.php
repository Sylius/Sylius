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
use Sylius\Behat\Page\Admin\Product\ShowPageInterface as AdminProductShowPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
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
     * @var ShowPageInterface
     */
    private $productShowPage;

    /**
     * @var AdminProductShowPageInterface
     */
    private $adminProductShowPage;

    /**
     * @var IndexPageInterface
     */
    private $adminProductIndexPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ShowPageInterface $productShowPage
     * @param AdminProductShowPageInterface $adminProductShowPage
     * @param IndexPageInterface $adminProductIndexPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ShowPageInterface $productShowPage,
        AdminProductShowPageInterface $adminProductShowPage,
        IndexPageInterface $adminProductIndexPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productShowPage = $productShowPage;
        $this->adminProductShowPage = $adminProductShowPage;
        $this->adminProductIndexPage = $adminProductIndexPage;
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
        $this->adminProductShowPage->open(['id' => $product->getId()]);

        $this->adminProductShowPage->deleteProduct();

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Then /^(this product) should not exist in the product catalog$/
     */
    public function productShouldNotExist(ProductInterface $product)
    {
        $this->adminProductIndexPage->open();

        expect($this->adminProductIndexPage->isThereProduct($product))->toBe(false);
    }
}
