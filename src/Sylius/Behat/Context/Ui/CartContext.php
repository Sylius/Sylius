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

use Sylius\Behat\Context\FeatureContext;
use Sylius\Behat\Page\Cart\CartSummaryPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartContext extends FeatureContext
{
    /**
     * @Then /^my cart total should be "([^"]*)"$/
     */
    public function myCartTotalShouldBe($total)
    {
        /** @var CartSummaryPage $cartSummaryPage */
        $cartSummaryPage = $this->getPage('Cart\CartSummaryPage');
        $cartSummaryPage->open();

        $this->assertSession()->elementTextContains('css', '#cart-summary', 'Grand total: '.$total);
    }

    /**
     * @Given /^my cart taxes should be "([^"]*)"$/
     */
    public function myCartTaxesShouldBe($taxesTotal)
    {
        /** @var CartSummaryPage $cartSummaryPage */
        $cartSummaryPage = $this->getPage('Cart\CartSummaryPage');
        $cartSummaryPage->open();

        $this->assertSession()->elementTextContains('css', '#cart-summary', 'Tax total: '.$taxesTotal);
    }
}
