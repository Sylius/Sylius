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

use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class PaymentsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_an_order(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/orders/nAWw2jewpA', server: $header);
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/payments/' . $orderResponse['payments'][0]['id'],
            server: $header,
        );
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin/get_payment_response', Response::HTTP_OK);
    }
}
