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

use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class CustomerGroupsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_customer_group(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'customer_group.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var CustomerGroupInterface $group */
        $group = $fixtures['group_vip'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/customer-groups/%s', $group->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer_group/get_customer_group_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_customer_groups(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'customer_group.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/customer-groups',
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer_group/get_customer_groups_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_customer_group(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/customer-groups',
            server: $header,
            content: json_encode([
                'name' => 'Special',
                'code' => 'special',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer_group/post_customer_group_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_an_existing_customer_group(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'customer_group.yaml']);

        /** @var CustomerGroupInterface $group */
        $group = $fixtures['group_vip'];

        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/customer-groups/' . $group->getCode(),
            server: $header,
            content: json_encode([
                'name' => 'Very Important People',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/customer_group/put_customer_group_response',
            Response::HTTP_OK,
        );
    }
}
