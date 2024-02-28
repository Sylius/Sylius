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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethodTranslationInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class PaymentMethodsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_payment_method(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'payment_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_cash_on_delivery'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/payment-methods/%s', $paymentMethod->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/payment_method/get_payment_method_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_payment_methods(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'payment_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/payment-methods', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/payment_method/get_payment_methods_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_payment_method(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
        ]);

        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request('POST', '/api/v2/admin/payment-methods', [], [], $header, json_encode([
            'code' => 'test',
            'name' => 'Test',
            'description' => 'Test',
            'translations' => [
                'en_US' => [
                    'name' => 'Test',
                    'description' => 'Test',
                    'instructions' => 'Testing instructions',
                ],
            ],
            'gatewayConfig' => [
                'factoryName' => 'paypal_express_checkout',
                'gatewayName' => 'paypal_express_checkout',
                'config' => [
                    'username' => 'test',
                    'password' => 'test',
                    'signature' => 'test',
                    'sandbox' => true,
                ],
            ],
            'enabled' => true,
        ]));

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/payment_method/create_payment_method_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_a_payment_method(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'payment_method.yaml',
        ]);

        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['paypal_payment_method'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/payment-methods/%s', $paymentMethod->getCode()),
            server: $header,
            content: json_encode([
                'translations' => [
                    'en_US' => [
                        '@id' => sprintf('/api/v2/admin/payment-method-translations/%s', $paymentMethod->getTranslation('en_US')->getId()),
                        'name' => 'Different name',
                        'description' => 'Different description',
                        'instructions' => 'Different instructions',
                    ],
                ],
                'position' => 1,
                'enabled' => false,
                'channels' => [
                    sprintf('/api/v2/admin/channels/%s', $fixtures['channel_mobile']->getCode()),
                ],
                'gatewayConfig' => [
                    '@id' => sprintf('/api/v2/admin/gateway-configs/%s', $paymentMethod->getGatewayConfig()->getId()),
                    'config' => [
                        'username' => 'differentTest',
                        'password' => 'differentTest',
                        'signature' => 'differentTest',
                        'sandbox' => false,
                    ],
                ],
            ]),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/payment_method/update_payment_method_response',
            Response::HTTP_OK,
        );

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/payment-methods/%s/gateway-config', $paymentMethod->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/payment_method/get_payment_method_gateway_config_after_update_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_removes_a_payment_method(): void
    {
        $this->setUpAdminContext();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['payment_method_cash_on_delivery'];

        $this->requestDelete(uri: '/api/v2/admin/payment-methods/'. $paymentMethod->getCode());

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_does_not_update_a_payment_method_with_duplicate_locale_translation(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'payment_method.yaml',
        ]);

        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $fixtures['paypal_payment_method'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/payment-methods/%s', $paymentMethod->getCode()),
            server: $header,
            content: json_encode([
                'translations' => [
                    'en_US' => [
                        'name' => 'Different name',
                        'description' => 'Different description',
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/payment_method/put_payment_method_with_duplicate_locale_translation',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_gets_a_payment_method_translation(): void
    {
        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'payment_method.yaml',
        ]);

        /** @var PaymentMethodTranslationInterface $paymentMethodTranslation */
        $paymentMethodTranslation = $fixtures['payment_method_cash_on_delivery_translation'];

        $this->requestGet(uri: '/api/v2/admin/payment-method-translations/' . $paymentMethodTranslation->getId());

        $this->assertResponseSuccessful('admin/payment_method/get_payment_method_translation_response');
    }
}
