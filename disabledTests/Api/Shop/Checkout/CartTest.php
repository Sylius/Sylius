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

namespace Sylius\Tests\Api\Shop\Checkout;

use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class CartTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_creates_empty_cart(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/orders',
            server: array_merge([], self::CONTENT_TYPE_HEADER),
            content: '{}',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/cart/create_cart',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_empty_cart_with_provided_locale(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/orders',
            server: array_merge(['HTTP_ACCEPT_LANGUAGE' => 'pl_PL'], self::CONTENT_TYPE_HEADER),
            content: '{}',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/cart/create_cart_with_locale',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_gets_empty_cart(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->requestGet(sprintf('/api/v2/shop/orders/%s', $tokenValue));

        $this->assertResponseSuccessful('shop/checkout/cart/get_empty_cart');
    }

    /** @test */
    public function it_gets_a_cart(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->requestGet(sprintf('/api/v2/shop/orders/%s', $tokenValue));

        $this->assertResponseSuccessful('shop/checkout/cart/get_cart');
    }

    /** @test */
    public function it_adds_item_to_order(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/shop/orders/%s/items', $tokenValue),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'productVariant' => '/api/v2/shop/product-variants/MUG_BLUE',
                'quantity' => 4,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/cart/add_item',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_allow_to_add_item_to_order_with_missing_fields(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/shop/orders/%s/items', $tokenValue),
            server: self::CONTENT_TYPE_HEADER,
            content: '{}',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/cart/add_item_with_missing_fields',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_removes_item_from_the_cart(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->requestGet(sprintf('/api/v2/shop/orders/%s', $tokenValue));
        $itemId = json_decode($this->client->getResponse()->getContent(), true)['items'][0]['id'];

        $this->requestDelete(sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, $itemId));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_does_not_allow_to_remove_item_from_the_cart_if_invalid_id_item(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->requestDelete(sprintf('/api/v2/shop/orders/%s/items/STRING-INSTEAD-OF-ID', $tokenValue));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_does_not_allow_to_remove_item_from_the_cart_if_invalid_order_token(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->requestDelete('/api/v2/shop/orders/INVALID/items/STRING-INSTEAD-OF-ID');

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_updates_item_quantity_in_cart(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->requestGet(sprintf('/api/v2/shop/orders/%s', $tokenValue));
        $itemId = json_decode($this->client->getResponse()->getContent(), true)['items'][0]['id'];

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, $itemId),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'quantity' => 5,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/cart/update_item_quantity',
        );
    }

    /** @test */
    public function it_does_not_allow_to_update_item_quantity_in_cart_with_missing_fields(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->requestGet(sprintf('/api/v2/shop/orders/%s', $tokenValue));
        $itemId = json_decode($this->client->getResponse()->getContent(), true)['items'][0]['id'];

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, $itemId),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: '{}',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/cart/update_item_quantity_with_missing_fields',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_does_not_allow_to_update_item_quantity_if_invalid_id_item(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, 'invalid-item-id'),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode(['quantity' => 5]),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_updates_cart(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/shop/orders/%s', $tokenValue),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'oliver@doe.com',
                'billingAddress' => [
                    'firstName' => 'Updated: Jane',
                    'lastName' => 'Updated: Doe',
                    'phoneNumber' => '123456789',
                    'countryCode' => 'US',
                    'provinceCode' => 'US-MI',
                    'city' => 'Updated: Nebraska',
                    'street' => 'Updated: Top secret',
                    'postcode' => '10001',
                ],
                'shippingAddress' => [
                    'firstName' => 'Updated: Jane',
                    'lastName' => 'Updated: Doe',
                    'phoneNumber' => '123456789',
                    'countryCode' => 'US',
                    'provinceCode' => 'US-MI',
                    'city' => 'Updated: Nebraska',
                    'street' => 'Updated: Top secret',
                    'postcode' => '121212',
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/cart/update_cart',
        );
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
                ['propertyPath' => '', 'message' => 'An empty order cannot be processed.'],
            ],
        );
    }

    /** @test */
    public function it_does_not_allow_update_without_required_billing_address(): void
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
            ],
        );
    }

    /** @test */
    public function it_does_not_allow_update_without_required_shipping_address(): void
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
            ],
        );
    }

    /** @test */
    public function it_does_not_allow_update_with_invalid_data(): void
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
                'billingAddress' => [
                    'countryCode' => 'invalid-code',
                ],
                'shippingAddress' => [],
                'couponCode' => 'invalid',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                ['propertyPath' => '', 'message' => 'The country invalid-code does not exist.'],
                ['propertyPath' => '', 'message' => 'The address without country cannot exist'],
                ['propertyPath' => 'couponCode', 'message' => 'Coupon code is invalid.'],
                ['propertyPath' => 'billingAddress.firstName', 'message' => 'Please enter first name.'],
                ['propertyPath' => 'billingAddress.lastName', 'message' => 'Please enter last name.'],
                ['propertyPath' => 'billingAddress.countryCode', 'message' => 'This value is not a valid country.'],
                ['propertyPath' => 'billingAddress.street', 'message' => 'Please enter street.'],
                ['propertyPath' => 'billingAddress.city', 'message' => 'Please enter city.'],
                ['propertyPath' => 'billingAddress.postcode', 'message' => 'Please enter postcode.'],
                ['propertyPath' => 'shippingAddress.firstName', 'message' => 'Please enter first name.'],
                ['propertyPath' => 'shippingAddress.lastName', 'message' => 'Please enter last name.'],
                ['propertyPath' => 'shippingAddress.countryCode', 'message' => 'Please select country.'],
                ['propertyPath' => 'shippingAddress.street', 'message' => 'Please enter street.'],
                ['propertyPath' => 'shippingAddress.city', 'message' => 'Please enter city.'],
                ['propertyPath' => 'shippingAddress.postcode', 'message' => 'Please enter postcode.'],
            ],
        );
    }

    /** @test */
    public function it_deletes_cart(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();

        $this->requestDelete(sprintf('/api/v2/shop/orders/%s', $tokenValue));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
