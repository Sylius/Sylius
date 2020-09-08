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

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Behat\Service\SprintfResponseEscaper;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    /** @var ApiClientInterface */
    private $cartsClient;

    /** @var ApiClientInterface */
    private $productsClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var ProductVariantResolverInterface */
    private $productVariantResolver;

    public function __construct(
        ApiClientInterface $cartsClient,
        ApiClientInterface $productsClient,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage,
        ProductVariantResolverInterface $productVariantResolver
    ) {
        $this->cartsClient = $cartsClient;
        $this->productsClient = $productsClient;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
        $this->productVariantResolver = $productVariantResolver;
    }

    /**
     * @When /^I clear my (cart)$/
     */
    public function iClearMyCart(string $tokenValue): void
    {
        $this->cartsClient->delete($tokenValue);
    }

    /**
     * @When /^I see the summary of my (cart)$/
     * @When /^the (?:visitor|administrator) try to see the summary of (?:customer|visitor)'s (cart)$/
     * @When /^the (?:visitor|customer) see the summary of (?:their) (cart)$/
     */
    public function iSeeTheSummaryOfMyCart(string $tokenValue): void
    {
        $this->cartsClient->show($tokenValue);
    }

    /**
     * @When /^I (?:add|added) (this product) to the (cart)$/
     * @When /^I (?:add|added) ("[^"]+" product) to the (cart)$/
     * @When /^I add (product "[^"]+") to the (cart)$/
     * @When /^the (?:visitor|customer) adds ("[^"]+" product) to the (cart)$/
     */
    public function iAddThisProductToTheCart(ProductInterface $product, string $tokenValue): void
    {
        $this->putProductToCart($product, $tokenValue);
    }

    /**
     * @When /^I add (\d+) of (them) to (?:the|my) (cart)$/
     * @When /^I add (\d+) (products "[^"]+") to the (cart)$/
     */
    public function iAddOfThemToMyCart(int $quantity, ProductInterface $product, string $tokenValue): void
    {
        $this->putProductToCart($product, $tokenValue, $quantity);
    }

    /**
     * @When /^I add ("[^"]+" variant of this product) to the (cart)$/
     */
    public function iAddVariantOfThisProductToTheCart(ProductVariantInterface $productVariant, string $tokenValue): void
    {
        $this->putProductVariantToCart($productVariant, $tokenValue, 1);
    }

    /**
     * @When /^I change (product "[^"]+") quantity to (\d+) in my (cart)$/
     * @When /^the (?:visitor|customer) change (product "[^"]+") quantity to (\d+) in his (cart)$/
     * @When /^the visitor try to change (product "[^"]+") quantity to (\d+) in the customer (cart)$/
     */
    public function iChangeQuantityToInMyCart(ProductInterface $product, int $quantity, string $tokenValue): void
    {
        $itemId = $this->geOrderItemIdForProductInCart($product, $tokenValue);
        $this->changeQuantityOfOrderItem($itemId, $quantity, $tokenValue);
    }

    /**
     * @When /^I remove (product "[^"]+") from the (cart)$/
     */
    public function iRemoveProductFromTheCart(ProductInterface $product, string $tokenValue): void
    {
        $itemId = $this->geOrderItemIdForProductInCart($product, $tokenValue);
        $this->removeOrderItemFromCart($itemId, $tokenValue);
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
     */
    public function myCartSTotalShouldBe(string $tokenValue, int $total): void
    {
        $response = $this->cartsClient->show($tokenValue);

        $responseTotal = $this->responseChecker->getValue($response, 'total');
        Assert::same($total, (int) $responseTotal);
    }

    /**
     * @Then /^my (cart) should be empty$/
     * @Then /^the visitor has no access to customer's (cart)$/
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
     * @Then I should see :productName with quantity :quantity in my cart
     * @Then /^the administrator should see ("[^"]+" product) with quantity ([^"]+) in the (?:customer|visitor) cart$/
     * @Then /^the (?:customer|visitor) should see (product "[^"]+") with quantity (\d+) in his cart$/
     */
    public function iShouldSeeWithQuantityInMyCart(string $productName, int $quantity): void {
        $cartResponse = $this->cartsClient->getLastResponse();
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

    private function putProductToCart(ProductInterface $product, string $tokenValue, int $quantity = 1): void
    {
        $request = Request::customItemAction(null, 'orders', $tokenValue, HttpRequest::METHOD_PATCH, 'items');

        $request->updateContent([
            'productCode' => $product->getCode(),
            'productVariantCode' => $this->productVariantResolver->getVariant($product)->getCode(),
            'quantity' => $quantity,
        ]);

        $this->cartsClient->executeCustomRequest($request);
    }

    private function putProductVariantToCart(ProductVariantInterface $productVariant, string $tokenValue, int $quantity = 1): void
    {
        $request = Request::customItemAction(null, 'orders', $tokenValue, HttpRequest::METHOD_PATCH, 'items');

        $request->updateContent([
            'productCode' => $productVariant->getProduct()->getCode(),
            'productVariantCode' => $productVariant->getCode(),
            'quantity' => $quantity,
        ]);

        $this->cartsClient->executeCustomRequest($request);
    }

    private function removeOrderItemFromCart(string $orderItemId, string $tokenValue): void
    {
        $request = Request::customItemAction(null, 'orders', $tokenValue, HttpRequest::METHOD_PATCH, 'remove');

        $request->updateContent(['orderItemId' => $orderItemId]);

        $this->cartsClient->executeCustomRequest($request);
    }

    private function getProductForItem(array $item): Response
    {
        if (!isset($item['variant'])) {
            throw new \InvalidArgumentException(
                'Expected array to have variant key and variant to have product, but one these keys is missing. Current array: ' .
                json_encode($item)
            );
        }

        $this->cartsClient->executeCustomRequest(Request::custom($item['variant'], HttpRequest::METHOD_GET));

        $response = $this->cartsClient->getLastResponse();

        $product = $this->responseChecker->getValue($response, 'product');

        $pathElements = explode('/', $product);

        $productCode = $pathElements[array_key_last($pathElements)];

        return $this->productsClient->show(StringInflector::nameToSlug($productCode));
    }

    private function getProductVariantForItem(array $item): Response
    {
        if (!isset($item['variant'])) {
            throw new \InvalidArgumentException(
                'Expected array to have variant key and variant to have product, but one these keys is missing. Current array: ' .
                json_encode($item)
            );
        }

        $this->cartsClient->executeCustomRequest(Request::custom($item['variant'], HttpRequest::METHOD_GET));

        return $this->cartsClient->getLastResponse();
    }

    private function getOrderItemProductCode(array $item): string
    {
        $pathElements = explode('/', $item['variant']['product']);

        return $pathElements[array_key_last($pathElements)];
    }

    private function geOrderItemIdForProductInCart(ProductInterface $product, string $tokenValue): ?string
    {
        $items = $this->responseChecker->getValue($this->cartsClient->show($tokenValue), 'items');

        foreach ($items as $item) {
            $response = $this->getProductForItem($item);
            if ($this->responseChecker->hasValue($response, 'code', $product->getCode())) {
                return (string) $item['id'];
            }
        }

        return null;
    }

    private function changeQuantityOfOrderItem(string $orderItemId, int $quantity, string $tokenValue): void
    {
        $request = Request::customItemAction(null, 'orders', $tokenValue, HttpRequest::METHOD_PATCH, 'change-quantity');

        $request->updateContent(['orderItemId' => $orderItemId, 'newQuantity' => $quantity]);

        $this->cartsClient->executeCustomRequest($request);
    }
}
