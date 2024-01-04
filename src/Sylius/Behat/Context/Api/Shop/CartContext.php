<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Shop;

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Behat\Service\SprintfResponseEscaper;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    public function __construct(
        private ApiClientInterface $shopClient,
        private ApiClientInterface $adminClient,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private ProductVariantResolverInterface $productVariantResolver,
        private IriConverterInterface $iriConverter,
        private RequestFactoryInterface $requestFactory,
        private string $apiUrlPrefix,
    ) {
    }

    /**
     * @When /^I clear my (cart)$/
     */
    public function iClearMyCart(string $tokenValue): void
    {
        $this->shopClient->delete(Resources::ORDERS, $tokenValue);

        $this->sharedStorage->set('cart_token', null);
    }

    /**
     * @When /^I see the summary of my ((?:|previous )cart)$/
     * @When /^the visitor try to see the summary of ((?:visitor|customer)'s cart)$/
     * @When /^the (?:visitor|customer) see the summary of ((?:|their )cart)$/
     */
    public function iSeeTheSummaryOfMyCart(?string $tokenValue): void
    {
        if ($tokenValue === null) {
            $tokenValue = $this->pickupCart();
        }

        $this->shopClient->show(Resources::ORDERS, $tokenValue);
    }

    /**
     * @When /^the administrator try to see the summary of ((?:visitor|customer)'s cart)$/
     */
    public function theAdministratorTryToSeeTheSummaryOfCart(?string $tokenValue): void
    {
        $this->adminClient->show(Resources::ORDERS, $tokenValue);
    }

    /**
     * @When /^I (?:add|added) (this product) to the (cart)$/
     * @When /^I (?:add|added) ("[^"]+" product) to the (cart)$/
     * @When /^I add (product "[^"]+") to the (cart)$/
     * @When /^the (?:visitor|customer) adds ("[^"]+" product) to the (cart)$/
     */
    public function iAddThisProductToTheCart(ProductInterface $product, ?string $tokenValue): void
    {
        $this->putProductToCart($product, $tokenValue);

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @When /^I add (products "([^"]+)" and "([^"]+)") to the cart$/
     * @When /^I add (products "([^"]+)", "([^"]+)" and "([^"]+)") to the cart$/
     */
    public function iAddMultipleProductsToTheCart(array $products): void
    {
        $tokenValue = $this->pickupCart();

        foreach ($products as $product) {
            $this->putProductToCart($product, $tokenValue);
        }
    }

    /**
     * @When /^I add (\d+) of (them) to (?:the|my) (cart)$/
     * @When /^I add(?:| again) (\d+) (products "[^"]+") to the (cart)$/
     * @When /^I try to add (\d+) (products "[^"]+") to the (cart)$/
     */
    public function iAddOfThemToMyCart(int $quantity, ProductInterface $product, ?string $tokenValue): void
    {
        $this->putProductToCart($product, $tokenValue, $quantity);

        $this->sharedStorage->set('product', $product);
    }

    /**
     * @When /^I add ("[^"]+" variant) of (this product) to the (cart)$/
     * @When /^I add ("[^"]+" variant) of (product "[^"]+") to the (cart)$/
     * @When /^I have ("[^"]+" variant) of (product "[^"]+") in the (cart)$/
     */
    public function iAddVariantOfThisProductToTheCart(
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ?string $tokenValue,
    ): void {
        $this->putProductVariantToCart($productVariant, $tokenValue, 1);
        $this->sharedStorage->set('variant', $productVariant);
    }

    /**
     * @When I add :product with :productOption :productOptionValue to the cart
     */
    public function iAddThisProductWithToTheCart(
        ProductInterface $product,
        string $productOption,
        string $productOptionValue,
    ): void {
        $productData = json_decode($this->shopClient->show(Resources::PRODUCTS, $product->getCode())->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        $variantIri = null;
        foreach ($productData['options'] as $optionIri) {
            $optionData = json_decode($this->shopClient->showByIri($optionIri)->getContent(), true, 512, \JSON_THROW_ON_ERROR);

            if ($optionData['name'] !== $productOption) {
                continue;
            }

            foreach ($optionData['values'] as $valueIri) {
                $optionValueData = json_decode($this->shopClient->showByIri($valueIri)->getContent(), true, 512, \JSON_THROW_ON_ERROR);

                if ($optionValueData['value'] !== $productOptionValue) {
                    continue;
                }

                $this->shopClient->index(Resources::PRODUCT_VARIANTS);
                $this->shopClient->addFilter('product', $productData['@id']);
                $this->shopClient->addFilter('optionValues', $valueIri);

                $variantsData = json_decode($this->shopClient->filter()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

                Assert::same($variantsData['hydra:totalItems'], 1);

                $variantIri = $variantsData['@id'] . '/' . $variantsData['hydra:member'][0]['code'];
            }
        }

        if (null === $variantIri) {
            throw new \DomainException(sprintf('Could not find variant with option "%s" set to "%s"', $productOption, $productOptionValue));
        }

        $tokenValue = $this->pickupCart();

        $request = $this->requestFactory->customItemAction(
            'shop',
            Resources::ORDERS,
            $tokenValue,
            HttpRequest::METHOD_POST,
            'items',
        );
        $request->updateContent([
            'productCode' => $productData['code'],
            'productVariant' => $variantIri,
            'quantity' => 1,
        ]);

        $this->shopClient->executeCustomRequest($request);
    }

    /**
     * @Given /^I change (product "[^"]+") quantity to (\d+)$/
     * @Given I change :productName quantity to :quantity
     * @When /^I change (product "[^"]+") quantity to (\d+) in my (cart)$/
     * @When /^the (?:visitor|customer) change (product "[^"]+") quantity to (\d+) in his (cart)$/
     * @When /^the visitor try to change (product "[^"]+") quantity to (\d+) in the customer (cart)$/
     * @When /^I try to change (product "[^"]+") quantity to (\d+) in my (cart)$/
     */
    public function iChangeQuantityToInMyCart(ProductInterface $product, int $quantity, ?string $tokenValue = null): void
    {
        if (null === $tokenValue && $this->sharedStorage->has('cart_token')) {
            $tokenValue = $this->sharedStorage->get('cart_token');
        }

        $itemResponse = $this->getOrderItemResponseFromProductInCart($product, $tokenValue);
        $this->changeQuantityOfOrderItem((string) $itemResponse['id'], $quantity, $tokenValue);
    }

    /**
     * @Given /^I removed (product "[^"]+") from the (cart)$/
     * @When /^I remove (product "[^"]+") from the (cart)$/
     */
    public function iRemoveProductFromTheCart(ProductInterface $product, string $tokenValue): void
    {
        $itemResponse = $this->getOrderItemResponseFromProductInCart($product, $tokenValue);
        $this->removeOrderItemFromCart((string) $itemResponse['id'], $tokenValue);
    }

    /**
     * @When /^I remove ("[^"]+" variant) from the (cart)$/
     */
    public function iRemoveVariantFromTheCart(ProductVariantInterface $variant, string $tokenValue): void
    {
        $itemResponse = $this->getOrderItemResponseFromProductVariantInCart($variant, $tokenValue);
        $this->removeOrderItemFromCart((string) $itemResponse['id'], $tokenValue);
    }

    /**
     * @When I pick up (my )cart (again)
     * @When I pick up cart in the :localeCode locale
     * @When I pick up cart without specifying locale
     * @When the visitor picks up the cart
     */
    public function iPickUpMyCart(?string $localeCode = null): void
    {
        $this->pickupCart($localeCode);
    }

    /**
     * @When I pick up cart using wrong locale
     */
    public function iPickUpMyCartUsingWrongLocale(): void
    {
        $this->pickupCart('en');
    }

    /**
     * @When I update my cart
     */
    public function iUpdateMyCart(): void
    {
        // Intentionally left blank
    }

    /**
     * @When /^I check details of my (cart)$/
     */
    public function iCheckDetailsOfMyCart(string $tokenValue): void
    {
        $this->shopClient->show(Resources::ORDERS, $tokenValue);
    }

    /**
     * @Then /^I should be notified that (this product) does not have sufficient stock$/
     * @Then /^I should be notified that (this product) has insufficient stock$/
     * @Then /^I should be notified that (this product) cannot be updated$/
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        Assert::true($this->responseChecker->hasViolationWithMessage(
            $this->shopClient->getLastResponse(),
            sprintf('The product variant with %s code does not have sufficient stock.', $product->getCode()),
        ));
    }

    /**
     * @Then /^I should not be notified that (this product) does not have sufficient stock$/
     * @Then /^I should not be notified that (this product) cannot be updated$/
     */
    public function iShouldNotBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        Assert::false($this->responseChecker->hasViolationWithMessage(
            $this->shopClient->getLastResponse(),
            sprintf('The product variant with %s code does not have sufficient stock.', $product->getCode()),
        ));
    }

    /**
     * @Then I should still be on product :product page
     */
    public function iShouldStillBeOnProductPage(ProductInterface $product): void
    {
        // Intentionally left blank
    }

    /**
     * @Then my cart's locale should be :locale
     */
    public function myCartLocaleShouldBe(LocaleInterface $locale): void
    {
        Assert::same(
            $this->responseChecker->getValue(
                $this->shopClient->getLastResponse(),
                'localeCode',
            ),
            $locale->getCode(),
        );
    }

    /**
     * @Then /^I should not have access to the summary of my (previous cart)$/
     */
    public function iShouldNotHaveAccessToTheSummaryOfMyCart(string $tokenValue): void
    {
        Assert::same($this->shopClient->show(Resources::ORDERS, $tokenValue)->getStatusCode(), Response::HTTP_NOT_FOUND);
    }

    /**
     * @Then my cart should be cleared
     */
    public function myCartShouldBeCleared(): void
    {
        $response = $this->shopClient->getLastResponse();

        Assert::true(
            $this->responseChecker->isDeletionSuccessful($response),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Cart has not been created.', $response),
        );
    }

    /**
     * @Then /^my (cart)'s total should be ("[^"]+")$/
     * @Then /^my (cart) total should be ("[^"]+")$/
     * @Then /^the (cart) total should be ("[^"]+")$/
     */
    public function myCartTotalShouldBe(string $tokenValue, int $total): void
    {
        $response = $this->shopClient->show(Resources::ORDERS, $tokenValue);
        $responseTotal = $this->responseChecker->getValue(
            $response,
            'total',
        );

        Assert::same($total, (int) $responseTotal, 'Expected totals are not the same. Received message:' . $response->getContent());
    }

    /**
     * @Then /^my (cart) items total should be ("[^"]+")$/
     */
    public function myCartItemsTotalShouldBe(string $tokenValue, int $total): void
    {
        $response = $this->shopClient->show(Resources::ORDERS, $tokenValue);
        $responseTotal = $this->responseChecker->getValue(
            $response,
            'itemsSubtotal',
        );

        Assert::same($total, (int) $responseTotal, 'Expected items totals are not the same. Received message:' . $response->getContent());
    }

    /**
     * @Then /^my included in price taxes should be ("[^"]+")$/
     */
    public function myIncludedInPriceTaxesShouldBe(int $taxTotal): void
    {
        $response = $this->shopClient->getLastResponse();

        Assert::same(
            $this->responseChecker->getValue($response, 'taxIncludedTotal'),
            $taxTotal,
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Expected totals are not the same.', $response),
        );
    }

    /**
     * @Then /^my (cart) should be empty$/
     * @Then /^(cart) should be empty with no value$/
     */
    public function myCartShouldBeEmpty(string $tokenValue): void
    {
        $response = $this->shopClient->show(Resources::ORDERS, $tokenValue);

        Assert::true(
            $this->responseChecker->isShowSuccessful($response),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Cart has not been created.', $response),
        );
    }

    /**
     * @Then /^the visitor has no access to (customer's cart)$/
     */
    public function theVisitorHasNoAccessToCustomer(?string $tokenValue): void
    {
        $response = $this->shopClient->show(Resources::ORDERS, $tokenValue);

        Assert::false(
            $this->responseChecker->isShowSuccessful($response),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Cart has not been created.', $response),
        );
    }

    /**
     * @Then I should be on my cart summary page
     */
    public function iShouldBeOnMyCartSummaryPage(): void
    {
        // Intentionally left blank
    }

    /**
     * @Then I should be notified that the product has been successfully added
     */
    public function iShouldBeNotifiedThatTheProductHasBeenSuccessfullyAdded(): void
    {
        $response = $this->shopClient->getLastResponse();
        Assert::true(
            $this->responseChecker->isCreationSuccessful($response),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Item has not been added.', $response),
        );
    }

    /**
     * @Then I should be notified that quantity of added product cannot be lower that 1
     */
    public function iShouldBeNotifiedThatQuantityOfAddedProductCannotBeLowerThan1(): void
    {
        $response = $this->shopClient->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Quantity of an order item cannot be lower than 1.', $response),
        );
    }

    /**
     * @Then /^I should see(?:| also) "([^"]+)" with unit price ("[^"]+") in my cart$/
     */
    public function iShouldSeeProductWithUnitPriceInMyCart(string $productName, int $unitPrice): void
    {
        $response = $this->shopClient->getLastResponse();

        foreach ($this->responseChecker->getValue($response, 'items') as $item) {
            if ($item['productName'] === $productName) {
                Assert::same($item['unitPrice'], $unitPrice);

                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('The product %s does not exist', $productName));
    }

    /**
     * @Then /^I should see(?:| also) "([^"]+)" with discounted unit price ("[^"]+") in my cart$/
     * @Then /^the product "([^"]+)" should have discounted unit price ("[^"]+") in the cart$/
     */
    public function iShouldSeeProductWithDiscountedUnitPriceInMyCart(string $productName, int $discountedUnitPrice): void
    {
        $response = $this->shopClient->getLastResponse();

        foreach ($this->responseChecker->getValue($response, 'items') as $item) {
            if ($item['productName'] === $productName) {
                Assert::same($item['discountedUnitPrice'], $discountedUnitPrice);

                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('The product %s does not exist', $productName));
    }

    /**
     * @Then /^the product "([^"]+)" should have total price ("[^"]+") in the cart$/
     * @Then /^total price of "([^"]+)" item should be ("[^"]+")$/
     */
    public function theProductShouldHaveTotalPriceInTheCart(string $productName, int $totalPrice): void
    {
        $response = $this->shopClient->getLastResponse();

        foreach ($this->responseChecker->getValue($response, 'items') as $item) {
            if ($item['productName'] === $productName) {
                Assert::same($item['total'], $totalPrice);

                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('The product %s does not exist', $productName));
    }

    /**
     * @Then there should be one item in my cart
     * @Then there should be one item named :productName in my cart
     */
    public function thereShouldBeOneItemInMyCart(?string $productName = null): void
    {
        $response = $this->shopClient->getLastResponse();
        $items = $this->responseChecker->getValue($response, 'items');

        Assert::count($items, 1);

        if (null !== $productName) {
            Assert::same($items[0]['productName'], $productName);
        }

        $this->sharedStorage->set('item', $items[0]);
    }

    /**
     * @Then /^there should be (\d+) item in my (cart)$/
     */
    public function thereShouldCountItemsInMyCart(int $count, string $cartToken): void
    {
        $response = $this->shopClient->show(Resources::ORDERS, $cartToken);
        $items = $this->responseChecker->getValue($response, 'items');

        Assert::count($items, $count);
    }

    /**
     * @Then /^(this item) should have name "([^"]+)"$/
     */
    public function thisItemShouldHaveName(array $item, string $productName): void
    {
        $response = $this->getProductForItem($item);

        Assert::true(
            $this->responseChecker->hasValue($response, 'name', $productName),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Name not found.', $response),
        );
    }

    /**
     * @Then /^(this item) should have variant "([^"]+)"$/
     */
    public function thisItemShouldHaveVariant(array $item, string $variantName): void
    {
        $response = $this->getProductVariantForItem($item);

        Assert::true(
            $this->responseChecker->hasValue($response, 'name', $variantName),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Name not found.', $response),
        );
    }

    /**
     * @Then /^(this item) should have code "([^"]+)"$/
     */
    public function thisItemShouldHaveCode(array $item, string $variantCode): void
    {
        $response = $this->getProductVariantForItem($item);

        Assert::true(
            $this->responseChecker->hasValue($response, 'code', $variantCode),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Name not found.', $response),
        );
    }

    /**
     * @Then /^(its|theirs) price should be decreased by ("[^"]+")$/
     * @Then /^(product "[^"]+") price should be decreased by ("[^"]+")$/
     */
    public function itsPriceShouldBeDecreasedBy(ProductInterface $product, int $amount): void
    {
        $pricing = $this->getExpectedPriceOfProductTimesQuantity($product);

        $this->compareItemPrice($product->getName(), $pricing - $amount);
    }

    /**
     * @Then /^(product "[^"]+") price should be discounted by ("[^"]+")$/
     */
    public function itsPriceShouldBeDiscountedBy(ProductInterface $product, int $amount): void
    {
        $pricing = $this->getExpectedPriceOfProductTimesQuantity($product);

        $this->compareItemPrice($product->getName(), $pricing - $amount, 'discountedUnitPrice');
    }

    /**
     * @Then /^(its|theirs) subtotal price should be decreased by ("[^"]+")$/
     */
    public function itsSubtotalPriceShouldBeDecreasedBy(ProductInterface $product, int $amount): void
    {
        $pricing = $this->getExpectedPriceOfProductTimesQuantity($product);

        $this->compareItemPrice($product->getName(), $pricing - $amount, 'subtotal');
    }

    /**
     * @Then product :product price should not be decreased
     */
    public function productPriceShouldNotBeDecreased(ProductInterface $product): void
    {
        $this->compareItemPrice($product->getName(), $this->getExpectedPriceOfProductTimesQuantity($product));
    }

    /**
     * @Then I should see :productName with quantity :quantity in my cart
     * @Then /^the (?:customer|visitor) should see product "([^"]+)" with quantity (\d+) in his cart$/
     */
    public function iShouldSeeWithQuantityInMyCart(string $productName, int $quantity): void
    {
        $this->checkProductQuantityByCustomer($this->shopClient->getLastResponse(), $productName, $quantity);
    }

    /**
     * @Then I should be informed that cart items are no longer available
     */
    public function iShouldBeInformedThatCartItemsAreNoLongerAvailable(): void
    {
        $response = $this->sharedStorage->get('response') ?? $this->shopClient->getLastResponse();

        Assert::same($response->getStatusCode(), 404);

        Assert::same($this->responseChecker->getResponseContent($response)['hydra:description'], 'Not Found');
    }

    /**
     * @Then /^the administrator should see "([^"]+)" product with quantity (\d+) in the (?:customer|visitor) cart$/
     */
    public function theAdministratorShouldSeeProductWithQuantityInTheCart(string $productName, int $quantity): void
    {
        $this->checkProductQuantityByAdmin($this->adminClient->getLastResponse(), $productName, $quantity);
    }

    /**
     * @Then /^the (?:visitor|customer) can see ("[^"]+" product) in the (cart)$/
     */
    public function theVisitorCanSeeProductInTheCart(
        ProductInterface $product,
        string $tokenValue,
        int $quantity = 1,
    ): void {
        $this->shopClient->show(Resources::ORDERS, $tokenValue);

        $this->iShouldSeeWithQuantityInMyCart($product->getName(), $quantity);
    }

    /**
     * @When /^I check items in my (cart)$/
     */
    public function iCheckItemsOfMyCart(string $tokenValue): void
    {
        $request = $this->requestFactory->customItemAction(
            'shop',
            Resources::ORDERS,
            $tokenValue,
            HttpRequest::METHOD_GET,
            'items',
        );
        $this->shopClient->executeCustomRequest($request);
    }

    /**
     * @Then /^my cart should have ("[^"]+") items total$/
     */
    public function myCartShouldHaveItemsTotal(int $itemsTotal): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->shopClient->getLastResponse(), 'itemsTotal'),
            $itemsTotal,
        );
    }

    /**
     * @Then /^my cart taxes should be ("[^"]+")$/
     */
    public function myCartTaxesShouldBe(int $taxTotal): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->shopClient->getLastResponse(), 'taxExcludedTotal'),
            $taxTotal,
        );
    }

    /**
     * @Then /^my cart included in price taxes should be ("[^"]+")$/
     */
    public function myCartTaxesIncludedInPriceShouldBe(int $taxTotal): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->shopClient->getLastResponse(), 'taxIncludedTotal'),
            $taxTotal,
        );
    }

    /**
     * @Then /^my cart should have (\d+) items of (product "([^"]+)")$/
     * @Then /^my cart should have quantity of (\d+) items of (product "([^"]+)")$/
     */
    public function myCartShouldHaveItems(int $quantity, ProductInterface $product): void
    {
        $response = $this->shopClient->getLastResponse();

        Assert::true($this->hasItemWithNameAndQuantity($response, $product->getName(), $quantity));
    }

    /**
     * @Then /^my cart shipping total should be ("[^"]+")$/
     * @Then I should not see shipping total for my cart
     * @Then /^my cart estimated shipping cost should be ("[^"]+")$/
     * @Then there should be no shipping fee
     * @Then my cart shipping should be for Free
     */
    public function myCartShippingFeeShouldBe(int $shippingTotal = 0): void
    {
        $response = $this->shopClient->getLastResponse();

        Assert::same(
            $this->responseChecker->getValue($response, 'shippingTotal'),
            $shippingTotal,
        );
    }

    /**
     * @Then I should be redirected to my cart summary page
     */
    public function iShouldBeRedirectedToMyCartSummaryPage(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Then /^I should have empty (cart)$/
     */
    public function iShouldHaveEmptyCart(string $tokenValue): void
    {
        $items = $this->responseChecker->getValue($this->shopClient->show(Resources::ORDERS, $tokenValue), 'items');

        Assert::same(count($items), 0, 'There should be an empty cart');
    }

    /**
     * @Then I should be unable to add it to the cart
     */
    public function iShouldBeUnableToAddItToTheCart(): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->sharedStorage->get('product_variant');

        $tokenValue = $this->pickupCart();
        $this->putProductVariantToCart($productVariant, $tokenValue);

        $response = $this->shopClient->getLastResponse();
        Assert::same($response->getStatusCode(), 422);
    }

    /**
     * @Then /^this product should have ([^"]+) "([^"]+)"$/
     */
    public function thisItemShouldHaveOptionValue(string $expectedOptionName, string $expectedOptionValueValue): void
    {
        $item = $this->sharedStorage->get('item');

        $optionValues = $this->responseChecker->getValue($this->shopClient->showByIri($item['variant']), 'optionValues');

        foreach ($optionValues as $optionValueIri) {
            $optionValue = $this->responseChecker->getResponseContent($this->shopClient->showByIri($optionValueIri));

            if ($optionValue['value'] !== $expectedOptionValueValue) {
                continue;
            }

            $option = $this->responseChecker->getResponseContent($this->shopClient->showByIri($optionValue['option']));

            if ($option['name'] === $expectedOptionName) {
                return;
            }
        }

        throw new \DomainException(
            sprintf('Could not find item with option "%s" set to "%s"', $expectedOptionName, $expectedOptionValueValue),
        );
    }

    /**
     * @Then /^I should see "([^"]+)" with original price ("[^"]+") in my cart$/
     */
    public function iShouldSeeWithOriginalPriceInMyCart(string $productName, int $originalPrice): void
    {
        $response = $this->shopClient->getLastResponse();

        foreach ($this->responseChecker->getValue($response, 'items') as $item) {
            if ($item['productName'] === $productName) {
                Assert::same($item['originalUnitPrice'], $originalPrice);

                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('The product %s does not exist', $productName));
    }

    /**
     * @Then /^I should see "([^"]+)" only with unit price ("[^"]+") in my cart$/
     */
    public function iShouldSeeOnlyWithUnitPriceInMyCart(string $productName, int $unitPrice): void
    {
        $response = $this->shopClient->getLastResponse();

        foreach ($this->responseChecker->getValue($response, 'items') as $item) {
            if ($item['productName'] === $productName) {
                Assert::same($item['unitPrice'], $unitPrice);
                Assert::false(isset($item['originalPrice']));

                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('The product %s does not exist', $productName));
    }

    private function pickupCart(?string $localeCode = null): string
    {
        $request = $this->requestFactory->custom(
            sprintf('%s/shop/orders', $this->apiUrlPrefix),
            HttpRequest::METHOD_POST,
            ['HTTP_ACCEPT_LANGUAGE' => $localeCode ?? ''],
        );

        $this->shopClient->executeCustomRequest($request);

        $tokenValue = $this->responseChecker->getValue($this->shopClient->getLastResponse(), 'tokenValue');

        $this->sharedStorage->set('cart_token', $tokenValue);

        return $tokenValue;
    }

    private function putProductToCart(ProductInterface $product, ?string $tokenValue, int $quantity = 1): void
    {
        $tokenValue ??= $this->pickupCart();

        $request = $this->requestFactory->customItemAction(
            'shop',
            Resources::ORDERS,
            $tokenValue,
            HttpRequest::METHOD_POST,
            'items',
        );
        $request->updateContent([
            'productVariant' => $this->iriConverter->getIriFromResource($this->productVariantResolver->getVariant($product)),
            'quantity' => $quantity,
        ]);

        $this->shopClient->executeCustomRequest($request);
    }

    private function putProductVariantToCart(ProductVariantInterface $productVariant, ?string $tokenValue, int $quantity = 1): void
    {
        $tokenValue ??= $this->pickupCart();

        $request = $this->requestFactory->customItemAction(
            'shop',
            Resources::ORDERS,
            $tokenValue,
            HttpRequest::METHOD_POST,
            'items',
        );
        $request->updateContent([
            'productVariant' => $this->iriConverter->getIriFromResource($productVariant),
            'quantity' => $quantity,
        ]);

        $this->shopClient->executeCustomRequest($request);
    }

    private function removeOrderItemFromCart(string $orderItemId, string $tokenValue): void
    {
        $request = $this->requestFactory->customItemAction(
            'shop',
            Resources::ORDERS,
            $tokenValue,
            HttpRequest::METHOD_DELETE,
            sprintf('items/%s', $orderItemId),
        );
        $this->shopClient->executeCustomRequest($request);
    }

    private function getProductForItem(array $item): Response
    {
        if (!isset($item['variant'])) {
            throw new \InvalidArgumentException(
                'Expected array to have variant key and variant to have product, but one these keys is missing. Current array: ' .
                serialize($item),
            );
        }

        $response = $this->shopClient->showByIri(urldecode($item['variant']));

        return $this->shopClient->showByIri(urldecode($this->responseChecker->getValue($response, 'product')));
    }

    private function getProductVariantForItem(array $item): Response
    {
        if (!isset($item['variant'])) {
            throw new \InvalidArgumentException(
                'Expected array to have variant key and variant to have product, but one these keys is missing. Current array: ' .
                serialize($item),
            );
        }

        $request = $this->requestFactory->custom($item['variant'], HttpRequest::METHOD_GET);
        $this->shopClient->executeCustomRequest($request);

        return $this->shopClient->getLastResponse();
    }

    private function getOrderItemResponseFromProductInCart(ProductInterface $product, string $tokenValue): ?array
    {
        $items = $this->responseChecker->getValue($this->shopClient->show(Resources::ORDERS, $tokenValue), 'items');

        foreach ($items as $item) {
            $response = $this->getProductForItem($item);
            if ($this->responseChecker->hasValue($response, 'code', $product->getCode())) {
                return $item;
            }
        }

        return null;
    }

    private function getOrderItemResponseFromProductVariantInCart(ProductVariantInterface $variant, string $tokenValue): ?array
    {
        $items = $this->responseChecker->getValue($this->shopClient->show(Resources::ORDERS, $tokenValue), 'items');

        foreach ($items as $item) {
            $response = $this->getProductVariantForItem($item);
            if ($this->responseChecker->hasValue($response, 'code', $variant->getCode())) {
                return $item;
            }
        }

        return null;
    }

    private function changeQuantityOfOrderItem(string $orderItemId, int $quantity, string $tokenValue): void
    {
        $request = $this->requestFactory->customItemAction(
            'shop',
            Resources::ORDERS,
            $tokenValue,
            HttpRequest::METHOD_PATCH,
            sprintf('items/%s', $orderItemId),
        );
        $request->updateContent(['quantity' => $quantity]);

        $this->shopClient->executeCustomRequest($request);

        $this->sharedStorage->set('response', $this->shopClient->getLastResponse());
    }

    private function hasItemWithNameAndQuantity(Response $response, string $productName, int $quantity): bool
    {
        $items = $this->responseChecker->getCollection($response);

        foreach ($items as $item) {
            if ($item['productName'] === $productName && $item['quantity'] === $quantity) {
                return true;
            }
        }

        return false;
    }

    private function checkProductQuantityByAdmin(Response $cartResponse, string $productName, int $quantity): void
    {
        $items = $this->responseChecker->getValue($cartResponse, 'items');

        foreach ($items as $item) {
            $productResponse = $this->getProductForItem($item);
            if ($this->responseChecker->hasTranslation($productResponse, 'en_US', 'name', $productName)) {
                $this->assertItemQuantity($productResponse, $item['quantity'], $quantity);

                return;
            }
        }

        throw new \InvalidArgumentException('Invalid item data');
    }

    private function checkProductQuantityByCustomer(Response $cartResponse, string $productName, int $quantity): void
    {
        $items = $this->responseChecker->getValue($cartResponse, 'items');

        foreach ($items as $item) {
            $productResponse = $this->getProductForItem($item);
            if ($this->responseChecker->hasValue($productResponse, 'name', $productName)) {
                $this->assertItemQuantity($cartResponse, $item['quantity'], $quantity);

                return;
            }
        }

        throw new \InvalidArgumentException('Invalid item data');
    }

    private function assertItemQuantity(Response $response, int $gotQuantity, int $expectedQuantity): void
    {
        Assert::same(
            $gotQuantity,
            $expectedQuantity,
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent(
                sprintf('Quantity did not match. Expected %s.', $expectedQuantity),
                $response,
            ),
        );
    }

    private function compareItemPrice(string $productName, int $productPrice, string $priceType = 'total'): void
    {
        $items = $this->responseChecker->getValue($this->shopClient->show(Resources::ORDERS, $this->sharedStorage->get('cart_token')), 'items');

        foreach ($items as $item) {
            if ($item['productName'] === $productName) {
                Assert::same($item[$priceType], $productPrice);

                return;
            }
        }

        throw new \InvalidArgumentException('Expected product does not exist');
    }

    private function getExpectedPriceOfProductTimesQuantity(ProductInterface $product): int
    {
        $cartResponse = $this->shopClient->show(Resources::ORDERS, $this->sharedStorage->get('cart_token'));
        $items = $this->responseChecker->getValue($cartResponse, 'items');

        foreach ($items as $item) {
            $productResponse = $this->getProductForItem($item);

            if ($this->responseChecker->hasValue($productResponse, 'name', $product->getName())) {
                $variantForItem = $this->getProductVariantForItem($item);

                return $this->responseChecker->getValue($variantForItem, 'price') * $item['quantity'];
            }
        }

        throw new \InvalidArgumentException(sprintf('Price for product %s had not been found', $product->getName()));
    }
}
