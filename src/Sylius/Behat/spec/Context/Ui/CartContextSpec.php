<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Ui;

use Behat\Mink\Mink;
use Behat\Mink\WebAssert;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\FeatureContext;
use Sylius\Behat\Extension\Factory\PageObjectFactory;
use Sylius\Behat\Page\Cart\CartSummaryPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartContextSpec extends ObjectBehavior
{
    function let(PageObjectFactory $pageObjectFactory, Mink $mink)
    {
        $this->setPageObjectFactory($pageObjectFactory);
        $this->setMink($mink);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\CartContext');
    }

    function it_is_feature_context()
    {
        $this->shouldHaveType(FeatureContext::class);
    }

    function it_checks_if_cart_has_given_total($pageObjectFactory, $mink, CartSummaryPage $cartSummaryPage, WebAssert $assert)
    {
        $pageObjectFactory->createPage('Cart\CartSummaryPage')->willReturn($cartSummaryPage);
        $cartSummaryPage->open()->shouldBeCalled();

        $mink->assertSession(null)->willReturn($assert);
        $assert->elementTextContains('css', '#cart-summary', 'Grand total: $100.00')->shouldBeCalled();

        $this->myCartTotalShouldBe('$100.00');
    }

    function it_checks_if_cart_has_given_tax_total($pageObjectFactory, $mink, CartSummaryPage $cartSummaryPage, WebAssert $assert)
    {
        $pageObjectFactory->createPage('Cart\CartSummaryPage')->willReturn($cartSummaryPage);
        $cartSummaryPage->open()->shouldBeCalled();

        $mink->assertSession(null)->willReturn($assert);
        $assert->elementTextContains('css', '#cart-summary', 'Tax total: $50.00')->shouldBeCalled();

        $this->myCartTaxesShouldBe('$50.00');
    }
}
