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
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class OrderContext implements Context
{
    public function __construct(
        private ApiClientInterface $shopClient,
        private ApiClientInterface $adminClient,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private IriConverterInterface $iriConverter,
        private SecurityServiceInterface $securityService,
        private RequestFactoryInterface $requestFactory,
        private string $apiUrlPrefix,
    ) {
    }

    /**
     * @Given /^I am changing (this order)'s payment method$/
     */
    public function iWantToChangeThisOrdersPaymentMethod(OrderInterface $order): void
    {
        $request = $this->requestFactory->custom(
            sprintf(
                '%s/shop/account/orders/%s/payments/%s',
                $this->apiUrlPrefix,
                $order->getTokenValue(),
                (string) $order->getPayments()->first()->getId(),
            ),
            HttpRequest::METHOD_PATCH,
            [],
            $this->shopClient->getToken(),
        );

        $this->sharedStorage->set('payment_method_patch_request', $request);
    }

    /**
     * @When I change my payment method to :paymentMethod
     */
    public function iChangeMyPaymentMethodTo(PaymentMethodInterface $paymentMethod): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $request = $this->requestFactory->custom(
            sprintf(
                '%s/shop/account/orders/%s/payments/%s',
                $this->apiUrlPrefix,
                $order->getTokenValue(),
                (string) $order->getPayments()->first()->getId(),
            ),
            HttpRequest::METHOD_PATCH,
            [],
            $this->shopClient->getToken(),
        );
        $request->setContent(['paymentMethod' => $this->iriConverter->getIriFromResource($paymentMethod)]);

        $this->shopClient->executeCustomRequest($request);
    }

    /**
     * @When I try to change my payment method to :paymentMethod
     */
    public function iTryToChangeMyPaymentMethodTo(PaymentMethodInterface $paymentMethod): void
    {
        $request = $this->sharedStorage->get('payment_method_patch_request');

        $request->setContent(['paymentMethod' => $this->iriConverter->getIriFromResource($paymentMethod)]);

        $this->shopClient->executeCustomRequest($request);
    }

    /**
     * @When I view the summary of my order :order
     */
    public function iViewTheSummaryOfMyOrder(OrderInterface $order): void
    {
        $this->shopClient->show(Resources::ORDERS, $order->getTokenValue());
    }

    /**
     * @When I try to see the order placed by a customer :customer
     */
    public function iTryToSeeTheOrderPlacedByACustomer(CustomerInterface $customer): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        Assert::eq($order->getCustomer(), $customer);

        $this->iViewTheSummaryOfMyOrder($order);
    }

    /**
     * @Then I should be able to access this order's details
     */
    public function iShouldBeAbleToAccessThisOrderDetails(): void
    {
        $response = $this->shopClient->show(Resources::ORDERS, $this->sharedStorage->get('cart_token'));

        Assert::same($response->getStatusCode(), Response::HTTP_OK);
        Assert::same(
            $this->responseChecker->getValue($this->shopClient->getLastResponse(), 'checkoutState'),
            OrderCheckoutStates::STATE_COMPLETED,
        );
        Assert::same(
            $this->sharedStorage->get('order_number'),
            $this->responseChecker->getValue($this->shopClient->getLastResponse(), 'number'),
        );
    }

    /**
     * @Then it should have the number :orderNumber
     */
    public function itShouldHaveTheNumber(string $orderNumber): void
    {
        Assert::same($this->responseChecker->getValue($this->shopClient->getLastResponse(), 'number'), $orderNumber);
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
        string $addressType,
    ): void {
        $address = $this->responseChecker->getValue($this->shopClient->getLastResponse(), ($addressType . 'Address'));

        $names = explode(' ', $customerName);

        Assert::same($address['firstName'], $names[0]);
        Assert::same($address['lastName'], $names[1]);
        Assert::same($address['street'], $street);
        Assert::same($address['postcode'], $postcode);
        Assert::same($address['city'], $city);
        Assert::same($address['countryCode'], $country->getCode());
    }

    /**
     * @Then I should see :amount items in the list
     */
    public function iShouldSeeItemsInTheList(int $amount): void
    {
        Assert::same(count($this->responseChecker->getValue($this->shopClient->getLastResponse(), 'items')), $amount);
    }

    /**
     * @Then the product named :productName should be in the items list
     */
    public function theProductShouldBeInTheItemsList(string $productName): void
    {
        $items = $this->responseChecker->getValue($this->shopClient->getLastResponse(), 'items');

        foreach ($items as $item) {
            if ($item['productName'] === $productName) {
                return;
            }
        }

        throw new \InvalidArgumentException('There is no product with given name.');
    }

    /**
     * @Then /^the (shipment) status should be "([^"]+)"$/
     * @Then /^I should see its (payment) status as "([^"]+)"$/
     */
    public function theShipmentStatusShouldBe(
        string $elementType,
        string $elementStatus,
        int $position = 0,
    ): void {
        $resources = $this->responseChecker->getValue($this->shopClient->getLastResponse(), $elementType . 's');

        $resource = $this->iriConverter->getResourceFromIri($resources[$position]['@id']);

        Assert::same(ucfirst($resource->getState()), $elementStatus);
    }

    /**
     * @Then /^the order's (shipment) status should be "([^"]+)"$/
     * @Then /^I should see its order's (payment) status as "([^"]+)"$/
     */
    public function iShouldSeeItsOrderSStatusAs(string $elementType, string $orderElementState): void
    {
        if ($elementType === 'shipment') {
            $elementType = 'shipping';
        }

        Assert::same(
            $orderElementState,
            StringInflector::codeToName(
                $this->responseChecker->getValue(
                    $this->shopClient->getLastResponse(),
                    $elementType . 'State',
                ),
            ),
        );
    }

    /**
     * @Then I should see :provinceName as province in the :addressType address
     */
    public function iShouldSeeAsProvinceInTheShippingAddress(string $provinceName, string $addressType): void
    {
        $address = $this->responseChecker->getValue($this->shopClient->getLastResponse(), ($addressType . 'Address'));

        Assert::same($address['provinceName'], $provinceName);
    }

    /**
     * @Then /^I should see ("[^"]+") as order's subtotal$/
     */
    public function iShouldSeeAsOrderSSubtotal(int $expectedSubtotal): void
    {
        $items = $this->responseChecker->getValue($this->shopClient->getLastResponse(), 'items');

        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal = $subtotal + $item['subtotal'];
        }

        Assert::same($subtotal, $expectedSubtotal);
    }

    /**
     * @Then /^I should see ("[^"]+") as order's total$/
     */
    public function iShouldSeeAsOrderSTotal(int $total): void
    {
        Assert::same($this->responseChecker->getValue($this->shopClient->getLastResponse(), 'total'), $total);
    }

    /**
     * @Then /^I should see that I have to pay ("[^"]+") for this order$/
     */
    public function iShouldSeeIHaveToPayForThisOrder(int $paymentAmount): void
    {
        $response = $this->shopClient->showByIri(
            $this->responseChecker->getValue($this->shopClient->getLastResponse(), 'payments')[0]['@id'],
        );

        Assert::same($this->responseChecker->getValue($response, 'amount'), $paymentAmount);
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

    /**
     * @Then /^the ("[^"]+" product) should have unit prices discounted by ("[^"]+"), ("[^"]+") and ("[^"]+")$/
     */
    public function theShouldHaveUnitPricesDiscountedFor(ProductInterface $product, int $amountOne, int $amountTwo, int $amountThree): void
    {
        Assert::true($this->hasCorrectAmountsDistributedOnAdjustments([$amountOne, $amountTwo, $amountThree], $product));
    }

    /**
     * @Then /^the ("[^"]+" product) should have unit prices discounted by ("[^"]+") and ("[^"]+")$/
     */
    public function theShouldHaveUnitPricesDiscountedForAnd(ProductInterface $product, int $amountOne, int $amountTwo): void
    {
        Assert::true($this->hasCorrectAmountsDistributedOnAdjustments([$amountOne, $amountTwo], $product));
    }

    /**
     * @Then I should have chosen :paymentMethod payment method
     */
    public function iShouldHaveChosenPaymentMethodForMyOrder(PaymentMethodInterface $paymentMethod): void
    {
        $payment = $this
            ->responseChecker
            ->getValue($this->shopClient->show(Resources::ORDERS, $this->sharedStorage->get('cart_token')), 'payments')[0]
        ;

        Assert::same($this->iriConverter->getIriFromResource($paymentMethod), $payment['method']);
    }

    /**
     * @Then I should not be able to see that order
     */
    public function iShouldNotBeAbleToSeeThatOrder(): void
    {
        Assert::false($this->responseChecker->isShowSuccessful($this->shopClient->getLastResponse()));
    }

    /**
     * @Then I should be denied an access to order list
     */
    public function iShouldDeniedAnAccessToOrderList(): void
    {
        Assert::true($this->responseChecker->hasAccessDenied($this->shopClient->getLastResponse()));
    }

    /**
     * @Then I should have :paymentMethod payment method on my order
     */
    public function iShouldHavePaymentMethodOnMyOrder(PaymentMethodInterface $paymentMethod): void
    {
        $paymentMethodIri = $this
            ->responseChecker
            ->getValue($this->shopClient->getLastResponse(), 'payments')[0]['method']
        ;

        Assert::same($this->iriConverter->getResourceFromIri($paymentMethodIri)->getCode(), $paymentMethod->getCode());
    }

    /**
     * @Then /^(the administrator) should know about (this additional note) for (this order made by "[^"]+")$/
     */
    public function theCustomerServiceShouldKnowAboutThisAdditionalNotes(
        AdminUserInterface $user,
        string $notes,
        OrderInterface $order,
    ): void {
        $this->securityService->logIn($user);

        Assert::same(
            $notes,
            $this->responseChecker->getValue($this->adminClient->show(Resources::ORDERS, $order->getTokenValue()), 'notes'),
        );
    }

    /**
     * @Then /^(the administrator) should see that (order placed by "[^"]+") has "([^"]+)" currency$/
     */
    public function theAdministratorShouldSeeThatThisOrderHasBeenPlacedIn(
        AdminUserInterface $user,
        OrderInterface $order,
        string $currency,
    ): void {
        $this->securityService->logIn($user);

        Assert::same(
            $currency,
            $this->responseChecker->getValue($this->adminClient->show(Resources::ORDERS, $order->getTokenValue()), 'currencyCode'),
        );
    }

    private function getAdjustmentsForOrder(): array
    {
        $response = $this->shopClient->subResourceIndex(Resources::ORDERS, 'adjustments', $this->sharedStorage->get('cart_token'));

        return $this->responseChecker->getCollection($response);
    }

    private function getAdjustmentsForOrderItem(string $itemId): array
    {
        $response = $this->shopClient->customAction(
            sprintf('%s/shop/orders/%s/items/%s/adjustments', $this->apiUrlPrefix, $this->sharedStorage->get('cart_token'), $itemId),
            HttpRequest::METHOD_GET,
        );

        return $this->responseChecker->getCollection($response);
    }

    private function geOrderItemIdForProductInCart(ProductInterface $product, string $tokenValue): ?string
    {
        $items = $this->responseChecker->getValue($this->shopClient->show(Resources::ORDERS, $tokenValue), 'items');

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
                json_encode($item),
            );
        }

        $request = $this->requestFactory->custom($item['variant'], HttpRequest::METHOD_GET);
        $this->shopClient->executeCustomRequest($request);

        return $this->shopClient->showByIri($this->responseChecker->getValue($this->shopClient->getLastResponse(), 'product'));
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

    private function hasCorrectAmountsDistributedOnAdjustments(array $amounts, ProductInterface $product): bool
    {
        $itemId = $this->geOrderItemIdForProductInCart($product, $this->sharedStorage->get('cart_token'));
        $adjustments = $this->getAdjustmentsForOrderItem($itemId);

        /** @var int $index */
        foreach ($adjustments as $index => $adjustment) {
            if (-$amounts[$index] !== $adjustment['amount']) {
                return false;
            }
        }

        return true;
    }
}
