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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ZonesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_zone(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'zones.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ZoneInterface $zone */
        $zone = $fixtures['zone_eu'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/zones/%s', $zone->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/zone/get_zone_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_zone(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'zones.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/zones',
            server: $header,
            content: json_encode([
                'code' => 'US',
                'name' => 'UnitedStates',
                'type' => 'province',
                'members' => [
                    ['code' => 'AL'],
                    ['code' => 'CA'],
                    ['code' => 'NY'],
                ],
            ]),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/zone/create_zone_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_zone(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'zones.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ZoneInterface $zone */
        $zone = $fixtures['zone_eu'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/zones/%s', $zone->getCode()),
            server: $header,
            content: json_encode([
                'name' => 'EuropeanUnion',
            ]),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/zone/update_zone_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_a_zone(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'zones.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ZoneInterface $zone */
        $zone = $fixtures['zone_eu'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/zones/%s', $zone->getCode()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_gets_zones(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'zones.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/zones',
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/zone/get_zones_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_zone_members(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'zones.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ZoneInterface $zone */
        $zone = $fixtures['zone_eu'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/zones/%s/members', $zone->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/zone/get_zone_members_response',
            Response::HTTP_OK,
        );
    }
}
