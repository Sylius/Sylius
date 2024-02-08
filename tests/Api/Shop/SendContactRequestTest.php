<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        self::getContainer();

        $this->loadFixturesFromFiles(['channel.yaml']);

        $this->client->request(
            'POST',
            '/api/v2/shop/contact-requests',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            json_encode([
                'email' => 'customer@email.com',
                'message' => 'Example of message'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
        self::assertEmailCount(1);
        self::assertEmailAddressContains(self::getMailerMessage(), 'To', 'web@sylius.com');
    }

    /** @test */
    public function it_sends_contact_request_as_logged_in_user(): void
    {
        self::getContainer();

        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'authentication/customer.yaml']);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $authorizationHeader = $this->getAuthorizationHeaderAsCustomer($customer->getEmailCanonical(), 'sylius');

        $this->client->request(
            'POST',
            '/api/v2/shop/contact-requests',
            [],
            [],
            array_merge($authorizationHeader, self::CONTENT_TYPE_HEADER),
            json_encode([
                'email' => 'customer@email.com',
                'message' => 'Example of message'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
        self::assertEmailCount(1);
        self::assertEmailAddressContains(self::getMailerMessage(), 'To', 'web@sylius.com');
    }
}
