<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Composite\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Context\Api\Shop\CartContext;
use Sylius\Behat\Context\Ui\Shop\CartContext as UiCartContext;
use Sylius\Component\Core\Model\ProductInterface;

class CompositeCartContext implements Context
{
    public function __construct(
        private CartContext $apiCartContext,
        private UiCartContext $uiCartContext,
    ) {}

    /**
     * @When I add :product to the cart on the web store
     */
    public function iAddProductToTheCartOnTheWebStore(ProductInterface $product): void
    {
        $this->uiCartContext->iAddProductToTheCart($product);
    }

    /**
     * @When I check items in my cart using API
     */
    public function iCheckItemsInMyCartUsingAPI(): void
    {
        $this->apiCartContext->iPickUpMyCart();
    }
}
