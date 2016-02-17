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

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\Ui\CartContext;
use Sylius\Behat\Page\Cart\CartSummaryPage;
use Sylius\Behat\Page\Product\ProductShowPage;

/**
 * @mixin CartContext
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartContextSpec extends ObjectBehavior
{
    public function let(CartSummaryPage $cartSummaryPage, ProductShowPage $productShowPage)
    {
        $this->beConstructedWith($cartSummaryPage, $productShowPage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\CartContext');
    }

    function it_is_a_context()
    {
        $this->shouldImplement(Context::class);
    }

    function it_checks_if_cart_has_given_total(CartSummaryPage $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getGrandTotal()->willReturn('$100.00');

        $this->myCartTotalShouldBe('$100.00');
    }

    function it_checks_if_cart_has_given_tax_total(CartSummaryPage $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getTaxTotal()->willReturn('$50.00');

        $this->myCartTaxesShouldBe('$50.00');
    }

    function it_checks_if_cart_has_given_promotion_total(CartSummaryPage $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getPromotionTotal()->willReturn('$50.00');

        $this->myCartPromotionsShouldBe('$50.00');
    }
}
