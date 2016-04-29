<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Product\IndexPageInterface;
use Sylius\Behat\Page\Admin\Product\ShowPageInterface as AdminProductShowPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class ManagingProductsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var AdminProductShowPageInterface
     */
    private $showPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;


    public function __construct(
        SharedStorageInterface $sharedStorage,
        AdminProductShowPageInterface $showPage,
        IndexPageInterface $indexPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->showPage = $showPage;
        $this->indexPage = $indexPage;
    }

    /**
     * @When I delete the :product product
     */
    public function iDeleteProduct(ProductInterface $product)
    {
        $this->showPage->open(['id' => $product->getId()]);

        $this->showPage->deleteProduct();

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Then /^(this product) should not exist in the product catalog$/
     */
    public function productShouldNotExist(ProductInterface $product)
    {
        $this->indexPage->open();

        expect($this->indexPage->isThereProduct($product))->toBe(false);
    }
}
