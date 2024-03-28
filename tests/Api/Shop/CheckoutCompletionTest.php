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

use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CheckoutCompletionTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_does_not_allow_update_without_items(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'oliver@doe.com',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                ['propertyPath' => '', 'message' => 'An empty order cannot be completed.'],
            ],
        );
    }

    /** @test */
    public function it_does_not_allow_update_without_require_billing_address(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'oliver@doe.com',
                'shippingAddress' => [
                    'firstName' => 'Oliver',
                    'lastName' => 'Doe',
                    'phoneNumber' => '123456789',
                    'countryCode' => 'US',
                    'provinceCode' => 'US-MI',
                    'city' => 'New York',
                    'street' => 'Broadway',
                    'postcode' => '10001',
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
               ['propertyPath' => '', 'message' => 'Please provide a billing address.'],
            ]
        );
    }

    /** @test */
    public function it_does_not_allow_update_without_require_shipping_address(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
        ]);

        $tokenValue = $this->pickUpCart(channelCode: 'MOBILE');
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'oliver@doe.com',
                'billingAddress' => [
                    'firstName' => 'Oliver',
                    'lastName' => 'Doe',
                    'phoneNumber' => '123456789',
                    'countryCode' => 'US',
                    'provinceCode' => 'US-MI',
                    'city' => 'New York',
                    'street' => 'Broadway',
                    'postcode' => '10001',
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
               ['propertyPath' => '', 'message' => 'Please provide a shipping address.'],
            ]
        );
    }

    /** @test */
    public function it_prevents_from_order_completion_if_order_is_in_the_cart_state(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
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
            server: $this->buildHeaders(),
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
        $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $this->getFirstShipmentId($tokenValue));

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
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
            server: $this->buildHeaders(),
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
        $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $this->getFirstShipmentId($tokenValue));
        $this->dispatchPaymentMethodChooseCommand($tokenValue, 'BANK_TRANSFER', $this->getFirstPaymentId($tokenValue));

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
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
        $this->dispatchPaymentMethodChooseCommand($tokenValue, 'BANK_TRANSFER', $this->getFirstPaymentId($tokenValue));

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
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
            server: $this->buildHeaders(),
            content: json_encode([]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
        $this->assertResponse(
            $this->client->getResponse(),
            'shop/order_completion/order_with_free_non_shippable_items_response',
            Response::HTTP_OK,
        );
    }

    private function getFirstShipmentId(string $tokenValue): string
    {
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
        );

        $content = $this->client->getResponse()->getContent();
        Assert::notFalse($content);

        return (string) json_decode($content)->shipments[0]->id;
    }

    private function getFirstPaymentId(string $tokenValue): string
    {
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
        );

        $content = $this->client->getResponse()->getContent();
        Assert::notFalse($content);

        return (string) json_decode($content)->payments[0]->id;
    }

    /** @return array<string, string> */
    private function buildHeaders(): array
    {
        return $this
            ->headerBuilder()
            ->withMergePatchJsonContentType()
            ->withJsonLdAccept()
            ->build()
        ;
    }
}
