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
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class PaymentsTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;
    use OrderPlacerTrait;

    /** @test */
    public function it_gets_payment_from_placed_order(): void
    {
        $this->loadFixturesFromFiles(['authentication/customer.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $loginData = $this->logInShopUser('oliver@doe.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $loginData;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue, 'oliver@doe.com');

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA', [], [], $header);
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);


        $this->client->request('GET', '/api/v2/shop/payments/' . $orderResponse['payments'][0]['id'], [], [], $header);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shop/get_payment_response', Response::HTTP_OK);
    }
}
