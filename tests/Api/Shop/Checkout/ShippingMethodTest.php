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

use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class ShippingMethodTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_selects_shipping_method(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_dhl'];

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/shipments/%s', $tokenValue, $cart->getShipments()->first()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'shippingMethod' => $shippingMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/shipping_method/select_shipping_method',
        );
    }

    /** @test */
    public function it_does_not_allow_to_select_shipping_method_to_non_existing_shipment(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_dhl'];

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/shipments/%s', $tokenValue, '1234'),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'shippingMethod' => $shippingMethod->getCode(),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_allow_to_select_shipping_method_with_missing_fields(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/shipments/%s', $tokenValue, $cart->getShipments()->first()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: '{}',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/shipping_method/select_shipping_method_with_missing_fields',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_does_not_allow_to_select_shipping_method_with_invalid_shipping_method(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/shipments/%s', $tokenValue, $cart->getShipments()->first()->getId()),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: json_encode([
                'shippingMethod' => 'invalid',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                ['propertyPath' => '', 'message' => 'The shipping method with invalid code does not exist.'],
            ],
        );
    }
}
