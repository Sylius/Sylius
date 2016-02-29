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
use Sylius\Behat\Page\Product\ProductShowPage;
use Sylius\Behat\Page\Admin\Product\ProductShowPage as AdminProductShowPage;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Test\Services\SharedStorage;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ProductContextSpec extends ObjectBehavior
{
    public function let(
        SharedStorage $sharedStorage,
        ProductShowPage $productShowPage,
        AdminProductShowPage $adminProductShowPage
    ) {
        $this->beConstructedWith($sharedStorage, $productShowPage, $adminProductShowPage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\ProductContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_checks_if_i_am_be_able_to_access_product_page(
        $productShowPage,
        ProductInterface $product
    ) {
        $productShowPage->tryToOpen(['product' => $product])->shouldBeCalled();
        $productShowPage->isOpen(['product' => $product])->willReturn(true);
        
        $this->iShouldBeAbleToAccessProduct($product);
    }

    function it_checks_if_i_am_not_able_to_access_product_page(
        $productShowPage,
        ProductInterface $product
    ) {
        $productShowPage->tryToOpen(['product' => $product])->shouldBeCalled();
        $productShowPage->isOpen(['product' => $product])->willReturn(false);

        $this->iShouldNotBeAbleToAccessProduct($product);
    }

    function it_deletes_product(
        $adminProductShowPage,
        $sharedStorage,
        ProductInterface $product
    ) {
        $sharedStorage->get('product')->willReturn($product);
        
        $product->getName()->willReturn('Model');
        $product->getId()->willReturn(1);

        $adminProductShowPage->open(['id' => 1])->shouldBeCalled();
        $adminProductShowPage->deleteProduct()->shouldBeCalled();

        $this->iDeleteProduct('Model');
    }
}
