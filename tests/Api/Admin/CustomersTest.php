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

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class CustomersTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_customers(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'customer.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/customers',
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer/get_customers_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_customer(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'customer.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/customers/' . $customer->getId(),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer/get_customer_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_customer_statistics(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'customer.yaml',
            'channel.yaml',
            'order/fulfilled_order.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/customers/%s/statistics', $customer->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer/get_customer_statistics_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_customer(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/customers',
            server: $header,
            content: json_encode([
                'email' => 'api@example.com',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'birthday' => '2023-10-24T11:00:00.000Z',
                'gender' => 'm',
                'phoneNumber' => '+48123456789',
                'subscribedToNewsletter' => true,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer/post_customer_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_a_customer_with_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'customer_group.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var CustomerGroupInterface $group */
        $group = $fixtures['group_vip'];

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/customers',
            server: $header,
            content: json_encode([
                'user' => [
                    'plainPassword' => 'sylius',
                    'enabled' => false,
                    'verified' => true,
                ],
                'email' => 'api@example.com',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'birthday' => '2023-10-24T11:00:00.000Z',
                'gender' => 'm',
                'group' => '/api/v2/admin/customer-groups/' . $group->getCode(),
                'phoneNumber' => '+48123456789',
                'subscribedToNewsletter' => true,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer/post_customer_with_user_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_allow_creating_a_customer_with_invalid_email(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/customers',
            server: $header,
            content: json_encode([
                'email' => 'api@com',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer/post_customer_with_invalid_email_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_does_not_allow_creating_a_customer_with_invalid_name(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/customers',
            server: $header,
            content: json_encode([
                'email' => 'api@example.com',
                'firstName' => 'J',
                'lastName' => 'D',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer/post_customer_with_invalid_name_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_does_not_allow_creating_a_customer_with_invalid_gender(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/customers',
            server: $header,
            content: json_encode([
                'email' => 'api@example.com',
                'gender' => 'a',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer/post_customer_with_invalid_gender_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_customer(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'customer.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];
        /** @var CustomerGroupInterface $group */
        $group = $fixtures['group_premium'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/customers/' . $customer->getId(),
            server: $header,
            content: json_encode([
                'user' => [
                    '@id' => '/api/v2/admin/shop-users/' . $customer->getUser()->getId(),
                    'plainPassword' => '1sylius1',
                    'enabled' => false,
                    'verified' => false,
                ],
                'email' => 'api@example.com',
                'firstName' => 'John',
                'lastName' => 'Lim',
                'birthday' => '2023-09-24T11:00:00.000Z',
                'gender' => 'f',
                'group' => '/api/v2/admin/customer-groups/' . $group->getCode(),
                'phoneNumber' => '+48987654321',
                'subscribedToNewsletter' => true,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer/update_customer_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_does_not_allow_updating_a_customer_with_invalid_gender(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'customer.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/customers/' . $customer->getId(),
            server: $header,
            content: json_encode([
                'gender' => 'a',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer/update_customer_with_invalid_gender_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
