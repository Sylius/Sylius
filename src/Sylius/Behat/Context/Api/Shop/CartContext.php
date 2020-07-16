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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
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

    public function __construct(
        ApiClientInterface $cartsClient,
        ApiClientInterface $productsClient,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->cartsClient = $cartsClient;
        $this->productsClient = $productsClient;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When /^I clear my (cart)$/
     */
    public function iClearMyCart(OrderInterface $cart): void
    {
        $this->cartsClient->delete($cart->getTokenValue());
    }

    /**
     * @When /^I see the summary of my (cart)$/
     */
    public function iSeeTheSummaryOfMyCart(OrderInterface $cart): void
    {
        $this->cartsClient->show($cart->getTokenValue());
    }

    /**
     * @When /^I (?:add|added) (this product) to the (cart)$/
     */
    public function iAddThisProductToTheCart(ProductInterface $product, OrderInterface $cart): void
    {
        $this->putProductToCart($product, $cart->getTokenValue());
    }

    /**
     * @When /^I add (\d+) of (them) to (?:the|my) (cart)$/
     */
    public function iAddOfThemToMyCart(int $quantity, ProductInterface $product, OrderInterface $cart): void
    {
        $this->putProductToCart($product, $cart->getTokenValue(), $quantity);
    }

    /**
     * @When /^I remove (product "[^"]+") from the (cart)$/
     */
    public function iRemoveProductFromTheCart(ProductInterface $product, OrderInterface $cart): void
    {
        /** @var string $tokenValue */
        $tokenValue = $cart->getTokenValue();

        $items = $this->responseChecker->getValue($this->cartsClient->show($tokenValue), 'items');

        foreach ($items as $item) {
            $pathElements = explode('/', $item['variant']['product']);

            $productCode = $pathElements[array_key_last($pathElements)];

            if ($product->getCode() === $productCode) {
                $this->removeOrderItemFromCart((string) $item['id'], $tokenValue);
            }
        }
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
    public function myCartSTotalShouldBe(OrderInterface $cart, int $total): void
    {
        $response = $this->cartsClient->show($cart->getTokenValue());

        $responseTotal = $this->responseChecker->getValue($response, 'total');
        Assert::same($total, (int) $responseTotal);
    }

    /**
     * @Then /^my (cart) should be empty$/
     */
    public function myCartShouldBeEmpty(OrderInterface $order): void
    {
        $response = $this->cartsClient->show($order->getTokenValue());

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
     * @Then I should see :productName with quantity :quantity in my cart
     */
    public function iShouldSeeWithQuantityInMyCart(string $productName, int $quantity): void
    {
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

    private function putProductToCart(ProductInterface $product, string $tokenValue, int $quantity = 1): void
    {
        $request = Request::customItemAction('orders', $tokenValue, HttpRequest::METHOD_PATCH, 'items');

        $request->updateContent([
            'productCode' => $product->getCode(),
            'quantity' => $quantity,
        ]);

        $this->cartsClient->executeCustomRequest($request);
    }

    private function removeOrderItemFromCart(string $orderItemId, string $tokenValue): void
    {
        $request = Request::customItemAction('orders', $tokenValue, HttpRequest::METHOD_PATCH, 'remove');

        $request->updateContent(['orderItemId' => $orderItemId]);

        $this->cartsClient->executeCustomRequest($request);
    }

    private function getProductForItem(array $item): Response
    {
        if (!isset($item['variant']['product'])) {
            throw new \InvalidArgumentException(
                'Expected array to have variant key and variant to have product, but one these keys is missing. Current array: ' .
                json_encode($item)
            );
        }

        $pathElements = explode('/', $item['variant']['product']);

        $productCode = $pathElements[array_key_last($pathElements)];

        return $this->productsClient->show(StringInflector::nameToSlug($productCode));
    }
}
