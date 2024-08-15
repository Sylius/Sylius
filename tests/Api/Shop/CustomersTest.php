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
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class CustomersTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /** @test */
    public function it_gets_customer(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/shop_user.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/customers/' . $customer->getId(),
            server: $header,
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/customer/get_customer_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_logs_in(): void
    {
        $this->loadFixturesFromFiles(['authentication/shop_user.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/customers/token',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'oliver@doe.com',
                'password' => 'sylius',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/customer/log_in_customer_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_registers_a_new_customer(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/customers',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'shop@example.com',
                'password' => 'sylius',
                'subscribedToNewsletter' => true,
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_updates_customers_data(): void
    {
        $loadedData = $this->loadFixturesFromFiles(['authentication/shop_user.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $loadedData['customer_oliver'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/shop/customers/' . $customer->getId(),
            server: $header,
            content: json_encode([
                'email' => 'john.wick@tarasov.mob',
                'firstName' => 'John',
                'lastName' => 'Wick',
                'phoneNumber' => '666777888',
                'gender' => 'm',
                'birthday' => '2023-10-24T11:00:00.000Z',
                'subscribedToNewsletter' => true,
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/customer/put_customer_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_sends_reset_password_email(): void
    {
        $loadedData = $this->loadFixturesFromFiles(['authentication/shop_user.yaml', 'channel/channel.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $loadedData['customer_oliver'];

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/customers/reset-password',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => $customer->getEmailCanonical(),
                'localeCode' => 'en_US',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
        $this->assertEmailCount(1);
    }

    /** @test */
    public function it_validates_wrong_reset_password_request(): void
    {
        $this->loadFixturesFromFiles(['authentication/shop_user.yaml', 'channel/channel.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/customers/reset-password',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'wrong_email',
                'localeCode' => 'te_ST',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/customer/reset_password_validation_response', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_resets_account_password(): void
    {
        $loadedData = $this->loadFixturesFromFiles(['authentication/shop_user.yaml', 'channel/channel.yaml']);

        /** @var ShopUserInterface $shopUser */
        $shopUser = $loadedData['shop_user_oliver'];
        $shopUser->setPasswordResetToken('token');
        $shopUser->setPasswordRequestedAt(new \DateTime('now'));
        $this->getEntityManager()->flush();

        $this->client->request(
            method: 'PATCH',
            uri: '/api/v2/shop/customers/reset-password/token',
            server: self::PATCH_CONTENT_TYPE_HEADER,
            content: json_encode([
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }
}
