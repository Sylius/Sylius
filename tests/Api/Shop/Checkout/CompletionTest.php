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

final class CompletionTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_does_not_allow_to_complete_order_in_cart_state(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
            content: '{}',
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                ['propertyPath' => '', 'message' => 'Cannot complete as order is in a wrong state. Current: cart. Possible transitions: address.'],
            ],
        );
    }

    /** @test */
    public function it_does_not_allow_to_complete_order_in_addressed_state(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'shipping_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
            content: '{}',
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                ['propertyPath' => '', 'message' => 'Cannot complete as order is in a wrong state. Current: addressed. Possible transitions: address, skip_shipping, select_shipping.'],
            ],
        );
    }

    /** @test */
    public function it_does_not_allow_to_complete_order_in_shipping_selected_state(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue);
        $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $cart->getShipments()->first()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
            content: '{}',
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                ['propertyPath' => '', 'message' => 'Cannot complete as order is in a wrong state. Current: shipping_selected. Possible transitions: address, select_shipping, skip_payment, select_payment.'],
            ],
        );
    }

    /** @test */
    public function it_does_not_allow_to_complete_order_in_shipping_skipped_state(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_NFT', 3, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
            content: '{}',
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                ['propertyPath' => '', 'message' => 'Cannot complete as order is in a wrong state. Current: shipping_skipped. Possible transitions: address, skip_payment, select_payment.'],
            ],
        );
    }

    /** @test */
    public function it_completes_checkout_with_shippable_and_non_shippable_items_if_all_checkout_steps_have_been_completed(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $this->addItemToCart('MUG_NFT', 1, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue);
        $cart = $this->dispatchShippingMethodChooseCommand($tokenValue, 'DHL', $cart->getShipments()->first()->getId());
        $this->dispatchPaymentMethodChooseCommand($tokenValue, 'BANK_TRANSFER', $cart->getLastPayment()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
            content: '{}',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/completion/order_with_shippable_and_non_shippable_items',
        );
    }

    /** @test */
    public function it_completes_checkout_with_non_shippable_items_without_shipping_method_assigned(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_NFT', 1, $tokenValue);
        $cart = $this->updateCartWithAddress($tokenValue);
        $this->dispatchPaymentMethodChooseCommand($tokenValue, 'BANK_TRANSFER', $cart->getLastPayment()->getId());

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
            content: '{}',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/completion/order_with_non_shippable_items',
        );
    }

    /** @test */
    public function it_completes_checkout_with_free_non_shippable_items_without_shipping_method_and_payment_method_assigned(): void
    {
        $this->loadFixturesFromFiles([
            'channel.yaml',
            'cart.yaml',
        ]);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_NFT_FREE', 1, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->buildHeaders(),
            content: json_encode([]),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/checkout/completion/order_with_free_non_shippable_items',
        );
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
