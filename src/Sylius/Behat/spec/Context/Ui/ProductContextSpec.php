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
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\Ui\ProductContext;
use Sylius\Behat\Page\Admin\Product\IndexPageInterface;
use Sylius\Behat\Page\Admin\Product\ShowPageInterface as AdminProductShowPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @mixin ProductContext
 *
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ProductContextSpec extends ObjectBehavior
{
    public function let(
        SharedStorageInterface $sharedStorage,
        ShowPageInterface $productShowPage,
        AdminProductShowPageInterface $adminProductShowPage,
        IndexPageInterface $adminProductIndexPage
    ) {
        $this->beConstructedWith($sharedStorage, $productShowPage, $adminProductShowPage, $adminProductIndexPage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\ProductContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_checks_if_i_am_able_to_access_product_page(ShowPageInterface $productShowPage, ProductInterface $product)
    {
        $productShowPage->tryToOpen(['product' => $product])->shouldBeCalled();
        $productShowPage->isOpen(['product' => $product])->willReturn(true);
        
        $this->iShouldBeAbleToAccessProduct($product);
    }

    function it_throws_an_exception_if_i_am_not_able_to_access_product_page_when_i_should(
        ShowPageInterface $productShowPage,
        ProductInterface $product
    ) {
        $productShowPage->tryToOpen(['product' => $product])->shouldBeCalled();
        $productShowPage->isOpen(['product' => $product])->willReturn(false);

        $this->shouldThrow(NotEqualException::class)->during('iShouldBeAbleToAccessProduct',[$product]);
    }

    function it_checks_if_i_am_not_able_to_access_product_page(ShowPageInterface $productShowPage, ProductInterface $product)
    {
        $productShowPage->tryToOpen(['product' => $product])->shouldBeCalled();
        $productShowPage->isOpen(['product' => $product])->willReturn(false);

        $this->iShouldNotBeAbleToAccessProduct($product);
    }

    function it_throws_an_exception_if_i_am_able_to_access_product_page_when_i_should_not(
        ShowPageInterface $productShowPage,
        ProductInterface $product
    ) {
        $productShowPage->tryToOpen(['product' => $product])->shouldBeCalled();
        $productShowPage->isOpen(['product' => $product])->willReturn(true);

        $this->shouldThrow(NotEqualException::class)->during('iShouldNotBeAbleToAccessProduct',[$product]);
    }

    function it_deletes_a_product(
        AdminProductShowPageInterface $adminProductShowPage,
        SharedStorageInterface $sharedStorage,
        ProductInterface $product
    ) {
        $sharedStorage->set('product', $product)->shouldBeCalled();
        
        $product->getName()->willReturn('Model');
        $product->getId()->willReturn(1);

        $adminProductShowPage->open(['id' => 1])->shouldBeCalled();
        $adminProductShowPage->deleteProduct()->shouldBeCalled();

        $this->iDeleteProduct($product);
    }

    function it_checks_if_a_product_does_not_exist(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $adminProductIndexPage,
        ProductInterface $product
    ) {
        $sharedStorage->get('product')->willReturn($product);

        $adminProductIndexPage->open()->shouldBeCalled();
        $adminProductIndexPage->isThereProduct($product)->willReturn(false);

        $this->productShouldNotExist($product);
    }

    function it_throws_an_exception_if_a_product_exists_when_it_should_not(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $adminProductIndexPage,
        ProductInterface $product
    ) {
        $sharedStorage->get('product')->willReturn($product);

        $adminProductIndexPage->open()->shouldBeCalled();
        $adminProductIndexPage->isThereProduct($product)->willReturn(true);

        $this->shouldThrow(NotEqualException::class)->during('productShouldNotExist', [$product]);
    }
}
