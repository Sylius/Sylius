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
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var ProductShowPage
     */
    private $productShowPage;

    /**
     * @param ProductShowPage $productShowPage
     */
    public function __construct(ProductShowPage $productShowPage)
    {
        $this->productShowPage = $productShowPage;
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
}
