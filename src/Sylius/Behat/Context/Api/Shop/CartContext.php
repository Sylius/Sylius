<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Shop;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Behat\Service\SprintfResponseEscaper;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    /** @var ApiClientInterface */
    private $cartsClient;

    /** @var ApiClientInterface */
    private $ordersAdminClient;

    /** @var ApiClientInterface */
    private $productsClient;

    /** @var ApiClientInterface */
    private $productVariantsClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var ProductVariantResolverInterface */
    private $productVariantResolver;

    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(
        ApiClientInterface $cartsClient,
        ApiClientInterface $ordersAdminClient,
        ApiClientInterface $productsClient,
        ApiClientInterface $productVariantsClient,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage,
        ProductVariantResolverInterface $productVariantResolver,
        IriConverterInterface $iriConverter
    ) {
        $this->cartsClient = $cartsClient;
        $this->ordersAdminClient = $ordersAdminClient;
        $this->productsClient = $productsClient;
        $this->productVariantsClient = $productVariantsClient;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
        $this->productVariantResolver = $productVariantResolver;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @When /^I clear my (cart)$/
     */
    public function iClearMyCart(string $tokenValue): void
    {
        $this->cartsClient->delete($tokenValue);

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

        $this->cartsClient->show($tokenValue);
    }

    /**
     * @When /^the administrator try to see the summary of ((?:visitor|customer)'s cart)$/
     */
    public function theAdministratorTryToSeeTheSummaryOfCart(?string $tokenValue): void
    {
        $this->ordersAdminClient->show($tokenValue);
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
     * @When /^I add ("[^"]+" variant of this product) to the (cart)$/
     */
    public function iAddVariantOfThisProductToTheCart(ProductVariantInterface $productVariant, ?string $tokenValue): void
    {
        $this->putProductVariantToCart($productVariant, $tokenValue, 1);
    }

    /**
     * @When I add :product with :productOption :productOptionValue to the cart
     */
    public function iAddThisProductWithToTheCart(
        ProductInterface $product,
        string $productOption,
        string $productOptionValue
    ): void {
        $productData = json_decode($this->productsClient->show($product->getCode())->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        $variantIri = null;
        foreach ($productData['options'] as $optionIri) {
            $optionData = json_decode($this->productsClient->showByIri($optionIri)->getContent(), true, 512, \JSON_THROW_ON_ERROR);

            if ($optionData['name'] !== $productOption) {
                continue;
            }

            foreach ($optionData['values'] as $valueIri) {
                $optionValueData = json_decode($this->productsClient->showByIri($valueIri)->getContent(), true, 512, \JSON_THROW_ON_ERROR);

                if ($optionValueData['value'] !== $productOptionValue) {
                    continue;
                }

                $this->productVariantsClient->index();
                $this->productVariantsClient->addFilter('product', $productData['@id']);
                $this->productVariantsClient->addFilter('optionValues', $valueIri);

                $variantsData = json_decode($this->productVariantsClient->filter()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

                Assert::same($variantsData['hydra:totalItems'], 1);

                $variantIri = $variantsData['@id'] . '/' . $variantsData['hydra:member'][0]['code'];
            }
        }

        if (null === $variantIri) {
            throw new \DomainException(sprintf('Could not find variant with option "%s" set to "%s"', $productOption, $productOptionValue));
        }

        $tokenValue = $this->pickupCart();

        $request = Request::customItemAction('shop', 'orders', $tokenValue, HttpRequest::METHOD_PATCH, 'items');

        $request->updateContent([
            'productCode' => $productData['code'],
            'productVariant' => $variantIri,
            'quantity' => 1,
        ]);

        $this->cartsClient->executeCustomRequest($request);
    }

    /**
     * @When /^I change (product "[^"]+") quantity to (\d+) in my (cart)$/
     * @When /^the (?:visitor|customer) change (product "[^"]+") quantity to (\d+) in his (cart)$/
     * @When /^the visitor try to change (product "[^"]+") quantity to (\d+) in the customer (cart)$/
     * @When /^I try to change (product "[^"]+") quantity to (\d+) in my (cart)$/
     */
    public function iChangeQuantityToInMyCart(ProductInterface $product, int $quantity, string $tokenValue): void
    {
        $itemResponse = $this->getOrderItemResponseFromProductInCart($product, $tokenValue);
        $this->changeQuantityOfOrderItem((string) $itemResponse['id'], $quantity, $tokenValue);
    }

    /**
     * @When /^I remove (product "[^"]+") from the (cart)$/
     */
    public function iRemoveProductFromTheCart(ProductInterface $product, string $tokenValue): void
    {
        $itemResponse = $this->getOrderItemResponseFromProductInCart($product, $tokenValue);
        $this->removeOrderItemFromCart((string) $itemResponse['id'], $tokenValue);
    }

    /**
     * @When I pick up my cart (again)
     * @When I pick up cart in the :localeCode locale
     */
    public function iPickUpMyCart(?string $localeCode = null): void
    {
        $this->pickupCart($localeCode);
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
        $this->cartsClient->show($tokenValue);
    }

    /**
     * @Then /^I should be notified that (this product) does not have sufficient stock$/
     * @Then /^I should be notified that (this product) cannot be updated$/
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        Assert::true($this->responseChecker->hasViolationWithMessage(
            $this->cartsClient->getLastResponse(),
            sprintf('The product variant with %s code does not have sufficient stock.', $product->getCode())
        ));
    }

    /**
     * @Then /^I should not be notified that (this product) does not have sufficient stock$/
     * @Then /^I should not be notified that (this product) cannot be updated$/
     */
    public function iShouldNotBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        Assert::false($this->responseChecker->hasViolationWithMessage(
            $this->cartsClient->getLastResponse(),
            sprintf('The product variant with %s code does not have sufficient stock.', $product->getCode())
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
            $this->cartsClient->getLastResponse(),
            'localeCode'
        ),
            $locale->getCode()
        );
    }

    /**
     * @Then /^I don't have access to see the summary of my (previous cart)$/
     */
    public function iDoNotHaveAccessToSeeTheSummaryOfMyCart(string $tokenValue): void
    {
        Assert::same($this->cartsClient->show($tokenValue)->getStatusCode(), Response::HTTP_NOT_FOUND);
    }

    /**
     * @Then my cart should be cleared
     */
    public function myCartShouldBeCleared(): void
    {
        $response = $this->cartsClient->getLastResponse();

        Assert::true(
            $this->responseChecker->isDeletionSuccessful($response),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Cart has not been created.', $response)
        );
    }

    /**
     * @Then /^my (cart)'s total should be ("[^"]+")$/
     * @Then /^my (cart) total should be ("[^"]+")$/
     */
    public function myCartSTotalShouldBe(string $tokenValue, int $total): void
    {
        $response = $this->cartsClient->show($tokenValue);
        $responseTotal = $this->responseChecker->getValue(
            $response,
            'total'
        );

        Assert::same($total, (int) $responseTotal, 'Expected totals are not the same. Received message:' . $response->getContent());
    }

    /**
     * @Then /^my (cart) should be empty$/
     */
    public function myCartShouldBeEmpty(string $tokenValue): void
    {
        $response = $this->cartsClient->show($tokenValue);

        Assert::true(
            $this->responseChecker->isShowSuccessful($response),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Cart has not been created.', $response)
        );
    }

    /**
     * @Then /^the visitor has no access to (customer's cart)$/
     */
    public function theVisitorHasNoAccessToCustomer(?string $tokenValue): void
    {
        $response = $this->cartsClient->show($tokenValue);

        Assert::false(
            $this->responseChecker->isShowSuccessful($response),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Cart has not been created.', $response)
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
        $response = $this->cartsClient->getLastResponse();
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($response),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Item has not been added.', $response)
        );
    }

    /**
     * @Then I should be notified that quantity of added product cannot be lower that 1
     */
    public function iShouldBeNotifiedThatQuantityOfAddedProductCannotBeLowerThan1(): void
    {
        $response = $this->cartsClient->getLastResponse();
        Assert::false(
            $this->responseChecker->isUpdateSuccessful($response),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Quantity of an order item cannot be lower than 1.', $response)
        );
    }

    /**
     * @Then /^I should see "([^"]+)" with unit price ("[^"]+") in my cart$/
     */
    public function iShouldSeeProductWithUnitPriceInMyCart(string $productName, int $unitPrice): void
    {
        $response = $this->cartsClient->getLastResponse();

        foreach ($this->responseChecker->getValue($response, 'items') as $item) {
            if ($item['productName'] === $productName) {
                Assert::same($item['unitPrice'], $unitPrice);

                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('The product %s does not exist', $productName));
    }

    /**
     * @Then there should be one item in my cart
     */
    public function thereShouldBeOneItemInMyCart(): void
    {
        $response = $this->cartsClient->getLastResponse();
        $items = $this->responseChecker->getValue($response, 'items');

        Assert::count($items, 1);

        $this->sharedStorage->set('item', $items[0]);
    }

    /**
     * @Then /^there should be (\d+) item in my (cart)$/
     */
    public function thereShouldCountItemsInMyCart(int $count, string $cartToken): void
    {
        $response = $this->cartsClient->show($cartToken);
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
            $this->responseChecker->hasTranslation($response, 'en_US', 'name', $productName),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Name not found.', $response)
        );
    }

    /**
     * @Then /^(this item) should have variant "([^"]+)"$/
     */
    public function thisItemShouldHaveVariant(array $item, string $variantName): void
    {
        $response = $this->getProductVariantForItem($item);

        Assert::true(
            $this->responseChecker->hasTranslation($response, 'en_US', 'name', $variantName),
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Name not found.', $response)
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
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Name not found.', $response)
        );
    }

    /**
     * @Then /^(its|theirs) price should be decreased by ("[^"]+")$/
     * @Then /^(product "[^"]+") price should be decreased by ("[^"]+")$/
     */
    public function itsPriceShouldBeDecreasedBy(ProductInterface $product, int $amount): void
    {
        $pricing = $this->getExpectedPriceOfProductTimesQuantity($product);

        $this->compareItemSubtotal($product->getName(), $pricing - $amount);
    }

    /**
     * @Then product :product price should not be decreased
     */
    public function productPriceShouldNotBeDecreased(ProductInterface $product): void
    {
        $this->compareItemSubtotal($product->getName(), $this->getExpectedPriceOfProductTimesQuantity($product));
    }

    /**
     * @Then I should see :productName with quantity :quantity in my cart
     * @Then /^the (?:customer|visitor) should see product "([^"]+)" with quantity (\d+) in his cart$/
     */
    public function iShouldSeeWithQuantityInMyCart(string $productName, int $quantity): void
    {
        $this->checkProductQuantity($this->cartsClient->getLastResponse(), $productName, $quantity);
    }

    /**
     * @Then I should be informed that cart items are no longer available
     */
    public function iShouldBeInformedThatCartItemsAreNoLongerAvailable(): void
    {
        $response = $this->sharedStorage->get('response') ?? $this->cartsClient->getLastResponse();

        Assert::same($response->getStatusCode(), 404);

        Assert::same($this->responseChecker->getResponseContent($response)['message'], 'Not Found');
    }

    /**
     * @Then /^the administrator should see "([^"]+)" product with quantity (\d+) in the (?:customer|visitor) cart$/
     */
    public function theAdministratorShouldSeeProductWithQuantityInTheCart(string $productName, int $quantity): void
    {
        $this->checkProductQuantity($this->ordersAdminClient->getLastResponse(), $productName, $quantity);
    }

    /**
     * @Then /^the (?:visitor|customer) can see ("[^"]+" product) in the (cart)$/
     */
    public function theVisitorCanSeeProductInTheCart(
        ProductInterface $product,
        string $tokenValue,
        int $quantity = 1
    ): void {
        $this->cartsClient->show($tokenValue);

        $this->iShouldSeeWithQuantityInMyCart($product->getName(), $quantity);
    }

    /**
     * @When /^I check items in my (cart)$/
     */
    public function iCheckItemsOfMyCart(string $tokenValue): void
    {
        $request = Request::customItemAction('shop', 'orders', $tokenValue, HttpRequest::METHOD_GET, 'items');

        $this->cartsClient->executeCustomRequest($request);
    }

    /**
     * @Then /^my cart should have ("[^"]+") items total$/
     */
    public function myCartShouldHaveItemsTotal(int $itemsTotal): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->cartsClient->getLastResponse(), 'itemsTotal'),
            $itemsTotal
        );
    }

    /**
     * @Then /^my cart taxes should be ("[^"]+")$/
     */
    public function myCartTaxesShouldBe(int $taxTotal): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->cartsClient->getLastResponse(), 'taxTotal'),
            $taxTotal
        );
    }

    /**
     * @Then /^my cart should have (\d+) items of (product "([^"]+)")$/
     * @Then /^my cart should have quantity of (\d+) items of (product "([^"]+)")$/
     */
    public function myCartShouldHaveItems(int $quantity, ProductInterface $product): void
    {
        $response = $this->cartsClient->getLastResponse();

        Assert::true($this->hasItemWithNameAndQuantity($response, $product->getName(), $quantity));
    }

    /**
     * @Then /^my cart shipping total should be ("[^"]+")$/
     * @Then I should not see shipping total for my cart
     */
    public function myCartShippingFeeShouldBe(int $shippingTotal = 0): void
    {
        $response = $this->cartsClient->getLastResponse();

        Assert::same(
            $this->responseChecker->getValue($response, 'shippingTotal'),
            $shippingTotal
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
        $items = $this->responseChecker->getValue($this->cartsClient->show($tokenValue), 'items');

        Assert::same(count($items), 0, 'There should be an empty cart');
    }

    /**
     * @Then I should be unable to add it to the cart
     */
    public function iShouldBeUnableToAddItToTheCart(): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->sharedStorage->get('productVariant');

        $tokenValue = $this->pickupCart();
        $this->putProductVariantToCart($productVariant, $tokenValue);

        $response = $this->cartsClient->getLastResponse();
        Assert::same($response->getStatusCode(), 422);
    }

    /**
     * @Then /^(this product) should have ([^"]+) "([^"]+)"$/
     */
    public function thisItemShouldHaveOptionValue(ProductInterface $product, string $optionName, string $optionValue): void
    {
        $item = $this->sharedStorage->get('item');

        $variantData = json_decode($this->cartsClient->showByIri(urldecode($item['variant']))->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        foreach ($variantData['optionValues'] as $valueIri) {
            $optionValueData = json_decode($this->cartsClient->showByIri($valueIri)->getContent(), true, 512, \JSON_THROW_ON_ERROR);

            if ($optionValueData['value'] !== $optionValue) {
                continue;
            }

            $optionData = json_decode($this->cartsClient->showByIri($optionValueData['option'])->getContent(), true, 512, \JSON_THROW_ON_ERROR);

            if ($optionData['name'] !== $optionName) {
                continue;
            }

            return;
        }

        throw new \DomainException(sprintf('Could not find item with option "%s" set to "%s"', $optionName, $optionValue));
    }

    private function pickupCart(?string $localeCode = null): string
    {
        $this->cartsClient->buildCreateRequest();
        $this->cartsClient->addRequestData('localeCode', $localeCode);

        $tokenValue = $this->responseChecker->getValue($this->cartsClient->create(), 'tokenValue');

        $this->sharedStorage->set('cart_token', $tokenValue);

        return $tokenValue;
    }

    private function putProductToCart(ProductInterface $product, ?string $tokenValue, int $quantity = 1): void
    {
        $tokenValue = $tokenValue ?? $this->pickupCart();

        $request = Request::customItemAction('shop', 'orders', $tokenValue, HttpRequest::METHOD_PATCH, 'items');

        $request->updateContent([
            'productVariant' => $this->iriConverter->getIriFromItem($this->productVariantResolver->getVariant($product)),
            'quantity' => $quantity,
        ]);

        $this->cartsClient->executeCustomRequest($request);
    }

    private function putProductVariantToCart(ProductVariantInterface $productVariant, ?string $tokenValue, int $quantity = 1): void
    {
        $tokenValue = $tokenValue ?? $this->pickupCart();

        $request = Request::customItemAction('shop', 'orders', $tokenValue, HttpRequest::METHOD_PATCH, 'items');

        $request->updateContent([
            'productVariant' => $this->iriConverter->getIriFromItem($productVariant),
            'quantity' => $quantity,
        ]);

        $this->cartsClient->executeCustomRequest($request);
    }

    private function removeOrderItemFromCart(string $orderItemId, string $tokenValue): void
    {
        $request = Request::customItemAction(
            'shop',
            'orders',
            $tokenValue,
            HttpRequest::METHOD_DELETE,
            \sprintf('items/%s', $orderItemId)
        );

        $this->cartsClient->executeCustomRequest($request);
    }

    private function getProductForItem(array $item): Response
    {
        if (!isset($item['variant'])) {
            throw new \InvalidArgumentException(
                'Expected array to have variant key and variant to have product, but one these keys is missing. Current array: ' .
                $item
            );
        }

        $response = $this->cartsClient->showByIri(urldecode($item['variant']));

        return $this->cartsClient->showByIri(urldecode($this->responseChecker->getValue($response, 'product')));
    }

    private function getProductVariantForItem(array $item): Response
    {
        if (!isset($item['variant'])) {
            throw new \InvalidArgumentException(
                'Expected array to have variant key and variant to have product, but one these keys is missing. Current array: ' .
                $item
            );
        }

        $this->cartsClient->executeCustomRequest(Request::custom($item['variant'], HttpRequest::METHOD_GET));

        return $this->cartsClient->getLastResponse();
    }

    private function getOrderItemResponseFromProductInCart(ProductInterface $product, string $tokenValue): ?array
    {
        $items = $this->responseChecker->getValue($this->cartsClient->show($tokenValue), 'items');

        foreach ($items as $item) {
            $response = $this->getProductForItem($item);
            if ($this->responseChecker->hasValue($response, 'code', $product->getCode())) {
                return $item;
            }
        }

        return null;
    }

    private function changeQuantityOfOrderItem(string $orderItemId, int $quantity, string $tokenValue): void
    {
        $request = Request::customItemAction('shop', 'orders', $tokenValue, HttpRequest::METHOD_PATCH, sprintf('items/%s', $orderItemId));

        $request->updateContent(['quantity' => $quantity]);

        $this->cartsClient->executeCustomRequest($request);

        $this->sharedStorage->set('response', $this->cartsClient->getLastResponse());
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

    private function checkProductQuantity(
        Response $cartResponse,
        string $productName,
        int $quantity
    ): void {
        $items = $this->responseChecker->getValue($cartResponse, 'items');

        foreach ($items as $item) {
            $productResponse = $this->getProductForItem($item);

            if ($this->responseChecker->hasTranslation($productResponse, 'en_US', 'name', $productName)) {
                Assert::same(
                    $item['quantity'],
                    $quantity,
                    SprintfResponseEscaper::provideMessageWithEscapedResponseContent(
                        sprintf('Quantity did not match. Expected %s.', $quantity),
                        $cartResponse
                    )
                );
            }
        }
    }

    private function compareItemSubtotal(string $productName, int $productPrice): void
    {
        $items = $this->responseChecker->getValue($this->cartsClient->show($this->sharedStorage->get('cart_token')), 'items');

        foreach ($items as $item) {
            if ($item['productName'] === $productName) {
                Assert::same($item['total'], $productPrice);
            }

            return;
        }

        throw new \InvalidArgumentException('Expected product does not exist');
    }

    private function getExpectedPriceOfProductTimesQuantity(ProductInterface $product): int
    {
        $cartResponse = $this->cartsClient->show($this->sharedStorage->get('cart_token'));
        $items = $this->responseChecker->getValue($cartResponse, 'items');

        foreach ($items as $item) {
            $productResponse = $this->getProductForItem($item);

            if ($this->responseChecker->hasTranslation($productResponse, 'en_US', 'name', $product->getName())) {
                $variantForItem = $this->getProductVariantForItem($item);

                return $this->responseChecker->getValue($variantForItem, 'price') * $item['quantity'];
            }
        }

        throw new \InvalidArgumentException(sprintf('Price for product %s had not been found', $product->getName()));
    }
}
