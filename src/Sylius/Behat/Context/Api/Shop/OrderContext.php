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
use Sylius\Behat\Service\Converter\AdminToShopIriConverterInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class OrderContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ApiClientInterface */
    private $productsAdminClient;

    /** @var ApiClientInterface */
    private $productsShopClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var AdminToShopIriConverterInterface */
    private $adminToShopIriConverter;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ApiClientInterface $productsAdminClient,
        ApiClientInterface $productsShopClient,
        ResponseCheckerInterface $responseChecker,
        AdminToShopIriConverterInterface $adminToShopIriConverter,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->productsAdminClient = $productsAdminClient;
        $this->productsShopClient = $productsShopClient;
        $this->responseChecker = $responseChecker;
        $this->adminToShopIriConverter = $adminToShopIriConverter;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I view the summary of the order :order
     */
    public function iSeeTheOrder(OrderInterface $order): void
    {
        $this->client->show($order->getTokenValue());
    }

    /**
     * @Then I should be able to access this order's details
     */
    public function iShouldBeAbleToAccessThisOrderDetails(): void
    {
        $response = $this->client->show($this->sharedStorage->get('cart_token'));

        Assert::same($response->getStatusCode(), Response::HTTP_OK);
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'checkoutState'),
            OrderCheckoutStates::STATE_COMPLETED
        );
        Assert::same(
            $this->sharedStorage->get('order_number'),
            $this->responseChecker->getValue($this->client->getLastResponse(), 'number')
        );
    }

    /**
     * @Then it should has number :orderNumber
     */
    public function itShouldHasNumber(string $orderNumber): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'number'), $orderNumber);
    }

    /**
     * @Then I should see :customerName, :street, :postcode, :city, :country as :addressType address
     */
    public function iShouldSeeAsShippingAddress(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country,
        string $addressType
    ): void {
        $address = $this->responseChecker->getValue($this->client->getLastResponse(), ($addressType . 'Address'));

        $names = explode(' ', $customerName);

        Assert::same($address['firstName'], $names[0]);
        Assert::same($address['lastName'], $names[1]);
        Assert::same($address['street'], $street);
        Assert::same($address['postcode'], $postcode);
        Assert::same($address['city'], $city);
        Assert::same($address['countryCode'], $country->getCode());
    }

    /**
     * @Then /^I should see ("[^"]+") as order's total$/
     */
    public function iShouldSeeAsOrderSTotal(int $total): void
    {
        Assert::same($this->responseChecker->getValue($this->client->getLastResponse(), 'total'), $total);
    }

    /**
     * @Then :promotionName should be applied to my order
     * @Then :promotionName should be applied to my order shipping
     */
    public function shouldBeAppliedToMyOrder(string $promotionName): void
    {
        Assert::true($this->hasAdjustmentWithLabel($promotionName));
    }

    /**
     * @Then /^(this promotion) should give ("[^"]+") discount on shipping$/
     */
    public function thisPromotionShouldGiveDiscountOnShipping(PromotionInterface $promotion, int $discount): void
    {
        $adjustment = $this->getAdjustmentWithLabel($promotion->getName());
        Assert::notNull($adjustment);
        Assert::same($discount, $adjustment['amount']);
    }

    /**
     * @Then /^the ("[^"]+" product) should have unit price discounted by ("[^"]+")$/
     */
    public function theShouldHaveUnitPriceDiscountedFor(ProductInterface $product, int $amount): void
    {
        $discount = 0;
        $itemId = $this->geOrderItemIdForProductInCart($product, $this->sharedStorage->get('cart_token'));
        $adjustments = $this->getAdjustmentsForOrderItem($itemId);
        foreach ($adjustments as $adjustment) {
            $discount += $adjustment['amount'];
        }

        Assert::same(-$discount, $amount);
    }

    private function getAdjustmentsForOrder(): array
    {
        $response = $this->client->subResourceIndex('adjustments', $this->sharedStorage->get('cart_token'));

        return $this->responseChecker->getCollection($response);
    }

    private function getAdjustmentsForOrderItem(string $itemId): array
    {
        $response = $this->client->customAction(
            sprintf('/new-api/shop/orders/%s/items/%s/adjustments', $this->sharedStorage->get('cart_token'), $itemId),
            HttpRequest::METHOD_GET
        );

        return $this->responseChecker->getCollection($response);
    }

    private function geOrderItemIdForProductInCart(ProductInterface $product, string $tokenValue): ?string
    {
        $items = $this->responseChecker->getValue($this->client->show($tokenValue), 'items');

        foreach ($items as $item) {
            $response = $this->getProductForItem($item);
            if ($this->responseChecker->hasValue($response, 'code', $product->getCode())) {
                return (string) $item['id'];
            }
        }

        return null;
    }

    private function getProductForItem(array $item): Response
    {
        if (!isset($item['variant'])) {
            throw new \InvalidArgumentException(
                'Expected array to have variant key, but this key is missing. Current array: ' .
                json_encode($item)
            );
        }

        $this->client->executeCustomRequest(
            Request::custom($this->adminToShopIriConverter->convert($item['variant']), HttpRequest::METHOD_GET)
        );

        $product = $this->responseChecker->getValue($this->client->getLastResponse(), 'product');

        $pathElements = explode('/', $product);
        $productCode = $pathElements[array_key_last($pathElements)];

        return $this->productsShopClient->show(StringInflector::nameToSlug($productCode));
    }

    private function getAdjustmentWithLabel(string $label): ?array
    {
        $adjustments = $this->getAdjustmentsForOrder();
        $index = array_search($label, array_column($adjustments, 'label'));
        if ($index) {
            return $adjustments[$index];
        }

        return null;
    }

    private function hasAdjustmentWithLabel(string $label): bool
    {
        return $this->getAdjustmentWithLabel($label) !== null;
    }
}
