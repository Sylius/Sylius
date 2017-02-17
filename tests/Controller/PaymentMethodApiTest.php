<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Tests\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class PaymentMethodApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_denies_getting_payment_method_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/payment-methods/none');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_payment_method_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/payment-methods/none', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_payment_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');
        $paymentMethods = $this->loadFixturesFromFile('resources/payment_methods.yml');
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $paymentMethods['cash_on_delivery'];

        $this->client->request('GET', $this->getPaymentMethodUrl($paymentMethod), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'payment_method/show_response', Response::HTTP_OK);
    }

    /**
     * @param PaymentMethodInterface $paymentMethod
     *
     * @return string
     */
    private function getPaymentMethodUrl(PaymentMethodInterface $paymentMethod)
    {
        return '/api/v1/payment-methods/' . $paymentMethod->getCode();
    }
}
