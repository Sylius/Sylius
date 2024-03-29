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

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class SendContactRequestTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /** @test */
    public function it_sends_contact_request(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/contact',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'customer@email.com',
                'message' => 'Example of message',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
        self::assertEmailCount(1);
        self::assertEmailAddressContains(self::getMailerMessage(), 'To', 'web@sylius.com');
    }

    /** @test */
    public function it_sends_contact_request_as_logged_in_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'authentication/shop_user.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/contact',
            server: $header,
            content: json_encode([
                'message' => 'Example of message',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
        self::assertEmailCount(1);
        self::assertEmailAddressContains(self::getMailerMessage(), 'To', 'web@sylius.com');
    }
}
