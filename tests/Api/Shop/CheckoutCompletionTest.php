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

namespace Sylius\Tests\Api\Shop;

use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Model\Address;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\ContentType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class CheckoutCompletionTest extends JsonApiTestCase
{
    private MessageBusInterface $commandBus;

    /** @test */
    public function it_prevents_from_order_completion_if_order_is_in_the_cart_state(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: ContentType::APPLICATION_JSON_MERGE_PATCH,
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_completion/order_not_addressed_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_prevents_from_order_completion_if_order_has_been_addressed_but_does_not_have_a_shipping_method_assigned(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'shipping_method.yaml']);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: ContentType::APPLICATION_JSON_MERGE_PATCH,
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_completion/shipping_method_not_chosen_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_prevents_from_order_completion_if_order_has_shipping_method_assigned_but_the_payment_has_not_been_chosen(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $this->updateCartWithAddress($tokenValue);
        $this->chooseShippingMethod($tokenValue, $this->getFirstShipmentId($tokenValue));

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: ContentType::APPLICATION_JSON_MERGE_PATCH,
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_completion/payment_method_not_chosen_with_shipping_selected_state_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_prevents_from_checkout_completion_if_there_is_no_shippable_item_in_the_cart_and_order_is_in_addressed_state(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_NFT', 3, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: ContentType::APPLICATION_JSON_MERGE_PATCH,
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_completion/payment_method_not_chosen_with_shipping_skipped_state_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_allows_to_complete_checkout_with_shippable_and_non_shippable_items_if_all_checkout_steps_have_been_completed(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $this->addItemToCart('MUG_NFT', 1, $tokenValue);
        $this->updateCartWithAddress($tokenValue);
        $this->chooseShippingMethod($tokenValue, $this->getFirstShipmentId($tokenValue));
        $this->choosePaymentMethod($tokenValue, $this->getFirstPaymentId($tokenValue));

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: ContentType::APPLICATION_JSON_MERGE_PATCH,
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_completion/order_with_shippable_and_non_shippable_items_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_allows_to_complete_checkout_with_non_shippable_items_without_shipping_method_assigned(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_NFT', 1, $tokenValue);
        $this->updateCartWithAddress($tokenValue);
        $this->choosePaymentMethod($tokenValue, $this->getFirstPaymentId($tokenValue));

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: ContentType::APPLICATION_JSON_MERGE_PATCH,
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_completion/order_with_non_shippable_items_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_allows_to_complete_checkout_with_free_non_shippable_items_without_shipping_method_and_payment_method_assigned(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_NFT_FREE', 1, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: ContentType::APPLICATION_JSON_MERGE_PATCH,
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_completion/order_with_free_non_shippable_items_response',
            Response::HTTP_OK,
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get('sylius.command_bus');
    }

    private function pickUpCart(): string
    {
        $tokenValue = 'nAWw2jewpA';

        $pickupCartCommand = new PickupCart($tokenValue);
        $pickupCartCommand->setChannelCode('WEB');

        $this->commandBus->dispatch($pickupCartCommand);

        return $tokenValue;
    }

    private function addItemToCart(string $productVariantCode, int $quantity, string $tokenValue): void
    {
        $addItemToCartCommand = new AddItemToCart($productVariantCode, $quantity);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);

        $this->commandBus->dispatch($addItemToCartCommand);
    }

    private function updateCartWithAddress(string $tokenValue): void
    {
        $address = new Address();
        $address->setFirstName('John');
        $address->setLastName('Doe');
        $address->setCity('New York');
        $address->setStreet('Avenue');
        $address->setCountryCode('US');
        $address->setPostcode('90000');

        $updateCartCommand = new UpdateCart(email: 'sylius@example.com', billingAddress: $address);
        $updateCartCommand->setOrderTokenValue($tokenValue);

        $this->commandBus->dispatch($updateCartCommand);
    }

    private function chooseShippingMethod(string $tokenValue, string $shipmentId): void
    {
        $chooseShippingMethodCommand = new ChooseShippingMethod('DHL');
        $chooseShippingMethodCommand->setSubresourceId($shipmentId);
        $chooseShippingMethodCommand->setOrderTokenValue($tokenValue);

        $this->commandBus->dispatch($chooseShippingMethodCommand);
    }

    private function choosePaymentMethod(string $tokenValue, string $paymentId): void
    {
        $choosePaymentMethodCommand = new ChoosePaymentMethod('BANK_TRANSFER');
        $choosePaymentMethodCommand->setSubresourceId($paymentId);
        $choosePaymentMethodCommand->setOrderTokenValue($tokenValue);

        $this->commandBus->dispatch($choosePaymentMethodCommand);
    }

    private function getFirstShipmentId(string $tokenValue): string
    {
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
        );

        return (string) json_decode($this->client->getResponse()->getContent())->shipments[0]->id;
    }

    private function getFirstPaymentId(string $tokenValue): string
    {
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
        );

        return (string) json_decode($this->client->getResponse()->getContent())->payments[0]->id;
    }
}
