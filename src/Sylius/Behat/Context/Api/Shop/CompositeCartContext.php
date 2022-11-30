<?php

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Context\Setup\ShopSecurityContext;
use Sylius\Behat\Context\Ui\Shop\CartContext as UiCartContext;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

class CompositeCartContext implements Context
{
    public function __construct(
        private CartContext $apiCartContext,
        private UiCartContext $uiCartContext,
        private ShopSecurityContext $uiSecurityContext,
        private ShopSecurityContext $apiSecurityContext,
        private SharedStorageInterface $sharedStorage,
    ) {}

    /**
     * @Then /^I pick up my cart from the API$/
     */
    public function iPickupMyCartFromTheApi()
    {
        $this->apiCartContext->iPickUpMyCart(email: $this->sharedStorage->get('user')->getEmail());
    }

    /**
     * @Then /^I can continue the order on the API$/
     */
    public function iCanContinueTheOrderOnTheApi()
    {
        $tokenValue = $this->sharedStorage->get('cart_token');
        Assert::notNull($tokenValue, 'Expect token value to be not null when pickup cart from API');
    }

    /**
     * @When /^I add (this product) to the cart on the UI$/
     */
    public function iAddThisProductToTheCartOnTheUi(ProductInterface $product)
    {
        $this->uiCartContext->iAddProductToTheCart($product);
    }

    /**
     * @Then /^I am a logged in customer on the API and the UI$/
     */
    public function IAmALoggedInCustomerOnTheApiAndTheUi()
    {
        $this->apiSecurityContext->iAmLoggedInCustomer();
        $this->uiSecurityContext->iAmLoggedInAs($this->sharedStorage->get('user')->getEmail());
    }
}
