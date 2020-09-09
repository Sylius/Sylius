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
use Sylius\Component\Core\Formatter\StringInflector;
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
