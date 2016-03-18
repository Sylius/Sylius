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
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\Ui\CartContext;
use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\Shop\Cart\CartSummaryPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @mixin CartContext
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CartContextSpec extends ObjectBehavior
{
    public function let(
        SharedStorageInterface $sharedStorage,
        CartSummaryPageInterface $cartSummaryPage,
        ShowPageInterface $productShowPage
    ) {
        $this->beConstructedWith($sharedStorage, $cartSummaryPage, $productShowPage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\CartContext');
    }

    function it_is_a_context()
    {
        $this->shouldImplement(Context::class);
    }

    function it_checks_if_cart_has_given_total(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getGrandTotal()->willReturn('$100.00');

        $this->myCartTotalShouldBe('$100.00');
    }

    function it_throws_not_equal_exception_if_grand_total_is_incorrect(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getGrandTotal()->willReturn('$90.00');

        $this->shouldThrow(NotEqualException::class)->during('myCartTotalShouldBe', ['$100.00']);
    }

    function it_checks_if_cart_has_given_tax_total(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getTaxTotal()->willReturn('$50.00');

        $this->myCartTaxesShouldBe('$50.00');
    }

    function it_throws_not_equal_exception_if_tax_total_is_incorrect(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getTaxTotal()->willReturn('$40.00');

        $this->shouldThrow(NotEqualException::class)->during('myCartTaxesShouldBe', ['$50.00']);
    }

    function it_checks_if_cart_has_given_promotion_total(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getPromotionTotal()->willReturn('$50.00');

        $this->myDiscountShouldBe('$50.00');
    }

    function it_throws_not_equal_exception_if_promotion_total_is_incorrect(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getPromotionTotal()->willReturn('$40.00');

        $this->shouldThrow(NotEqualException::class)->during('myDiscountShouldBe', ['$50.00']);
    }

    function it_ensures_there_is_no_grand_total_info_on_the_page(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getGrandTotal()->willThrow(new ElementNotFoundException('"grand total" element is not present on the page'));

        $this->cartShouldBeEmptyWithNoValue();
    }

    function it_throws_exception_if_grand_total_is_present_on_the_page_but_should_not(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getGrandTotal()->willReturn('$10.00');

        $this->shouldThrow(new FailureException('Expected to get exception, none got.'))->during('cartShouldBeEmptyWithNoValue', []);
    }

    function it_ensures_there_is_no_shipping_fee_info_on_the_page(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getShippingTotal()->willThrow(new ElementNotFoundException('"shipping total" element is not present on the page'));

        $this->thereShouldBeNoShippingFee();
    }
    
    function it_throws_exception_if_shipping_total_is_present_on_the_page_but_should_not(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getShippingTotal()->willReturn('$10.00');
        
        $this->shouldThrow(new FailureException('Expected to get exception, none got.'))->during('thereShouldBeNoShippingFee', []);
    }

    function it_ensures_there_is_no_discount_info_on_the_page(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getPromotionTotal()->willThrow(new ElementNotFoundException('"promotion total" element is not present on the page'));

        $this->thereShouldBeNoDiscount();
    }

    function it_throws_exception_if_discount_info_is_present_on_the_page_but_should_not(CartSummaryPageInterface $cartSummaryPage)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $cartSummaryPage->getPromotionTotal()->willReturn('$10.00');

        $this->shouldThrow(new FailureException('Expected to get exception, none got.'))->during('thereShouldBeNoDiscount');
    }

    function it_checks_if_cart_item_has_correct_discount_price(CartSummaryPageInterface $cartSummaryPage, ProductInterface $product)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $product->getName()->willReturn('Test product');

        $cartSummaryPage->getItemDiscountPrice('Test product')->willReturn('€30.00');
        $cartSummaryPage->getItemRegularPrice('Test product')->willReturn('€50.00');

        $this->itsPriceShouldBeDecreasedBy($product, 2000);
    }

    function it_throws_not_equal_exception_if_cart_item_discount_is_incorrect(
        CartSummaryPageInterface $cartSummaryPage,
        ProductInterface $product
    ) {
        $cartSummaryPage->open()->shouldBeCalled();
        $product->getName()->willReturn('Test product');

        $cartSummaryPage->getItemDiscountPrice('Test product')->willReturn('€30.00');
        $cartSummaryPage->getItemRegularPrice('Test product')->willReturn('€50.00');

        $this->shouldThrow(NotEqualException::class)->during('itsPriceShouldBeDecreasedBy', [$product, 1000]);
    }

    function it_ensures_product_has_no_discount_price(CartSummaryPageInterface $cartSummaryPage, ProductInterface $product)
    {
        $cartSummaryPage->open()->shouldBeCalled();
        $product->getName()->willReturn('Test product');

        $cartSummaryPage->isItemDiscounted('Test product')->willReturn(false);

        $this->productPriceShouldNotBeDecreased($product);
    }

    function it_throws_exception_if_discount_price_is_present_on_the_page_but_should_not(
        CartSummaryPageInterface $cartSummaryPage,
        ProductInterface $product
    ) {
        $cartSummaryPage->open()->shouldBeCalled();
        $product->getName()->willReturn('Test product');

        $cartSummaryPage->isItemDiscounted('Test product')->willReturn(true);

        $this->shouldThrow(new FailureException('Expected <value>false</value>, but got <value>true</value>.'))->during('productPriceShouldNotBeDecreased', [$product]);
    }
}
