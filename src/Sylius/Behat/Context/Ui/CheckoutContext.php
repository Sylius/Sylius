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
use Sylius\Behat\Page\Product\ProductShowPage;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutContext extends FeatureContext
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @var RepositoryInterface
     */
    private $orderRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $productRepository
     * @param RepositoryInterface $orderRepository
     */
    public function __construct(SharedStorageInterface $sharedStorage, RepositoryInterface $productRepository, RepositoryInterface $orderRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Given I added product :name to the cart
     * @When I add product :name to the cart
     */
    public function iAddProductToTheCart($name)
    {
        $productShowPage = $this->openProductPage($name);
        $productShowPage->addToCart();
    }

    /**
     * @When /^I add (\d+) products "([^"]*)" to the cart$/
     */
    public function iAddProductsToTheCart($quantity, $name)
    {
        $productShowPage = $this->openProductPage($name);
        $productShowPage->addToCartWithQuantity($quantity);
    }

    /**
     * @When I proceed selecting :paymentMethodName payment method
     */
    public function iProceedSelectingOfflinePaymentMethod($paymentMethodName)
    {
        $checkoutAddressingPage = $this->getPage('Checkout\CheckoutAddressingStep')->open();
        $addressingDetails = array(
            'firstName' => 'John',
            'lastName' => 'Doe',
            'country' => 'France',
            'street' => '0635 Myron Hollow Apt. 711',
            'city' => 'North Bridget',
            'postcode' => '93-554',
            'phoneNumber' => '321123456'
        );
        $checkoutAddressingPage->fillAddressingDetails($addressingDetails);
        $checkoutAddressingPage->pressButton('Continue');

        $checkoutShippingPage = $this->getPage('Checkout\CheckoutShippingStep');
        $checkoutShippingPage->pressRadio('Free');
        $checkoutShippingPage->pressButton('Continue');

        $checkoutPaymentPage = $this->getPage('Checkout\CheckoutPaymentStep');
        $checkoutPaymentPage->pressRadio($paymentMethodName);
        $checkoutPaymentPage->pressButton('Continue');
    }

    /**
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        $checkoutFinalizePage = $this->getPage('Checkout\CheckoutFinalizeStep');
        $checkoutFinalizePage->assertRoute();
        $checkoutFinalizePage->clickLink('Place order');
    }

    /**
     * @Then I should see the thank you page
     */
    public function iShouldSeeTheThankYouPage()
    {
        $user = $this->sharedStorage->getCurrentResource('user');
        $customer = $user->getCustomer();
        $thankYouPage = $this->getPage('Checkout\CheckoutThankYouPage');
        $thankYouPage->assertRoute();
        $this->assertSession()->elementTextContains('css', '#thanks', sprintf('Thank you %s', $customer->getFullName()));
    }

    /**
     * @param $productName
     *
     * @return ProductShowPage
     *
     * @throws \Exception
     */
    private function openProductPage($productName)
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy(array('name' => $productName));
        if (null === $product) {
            throw new \Exception('Store has no product with name "'.$productName.'".');
        }

        return $this->getPage('Product\ProductShowPage')->openSpecificProductPage($product);
    }
}
