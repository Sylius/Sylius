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
use Symfony\Component\HttpFoundation\Response;

final class ZonesTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();

        parent::setUp();
    }

    /** @test */
    public function it_gets_zones(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'zones.yaml',
        ]);

        $this->requestGet('/api/v2/admin/zones');

        $this->assertResponse($this->client->getResponse(), 'admin/zone/get_zones');
    }

    /** @test */
    public function it_gets_a_zone(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'zones.yaml',
        ]);

        /** @var ZoneInterface $zone */
        $zone = $fixtures['zone_eu'];

        $this->requestGet(sprintf('/api/v2/admin/zones/%s', $zone->getCode()));

        $this->assertResponse($this->client->getResponse(), 'admin/zone/get_zone');
    }

    /** @test */
    public function it_creates_a_zone(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'zones.yaml',
        ]);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/zones',
            server: $this->headerBuilder()->withJsonLdContentType()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
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
            'admin/zone/create_zone',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_zone(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'zones.yaml',
        ]);

        /** @var ZoneInterface $zone */
        $zone = $fixtures['zone_eu'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/zones/%s', $zone->getCode()),
            server: $this->headerBuilder()->withJsonLdContentType()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
            content: json_encode([
                'name' => 'EuropeanUnion',
                'members' => [
                    ['code' => 'NL'],
                    ['code' => 'DE'],
                    ['code' => 'PL'],
                ],
            ]),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/zone/update_zone',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_a_zone(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'zones.yaml',
        ]);

        /** @var ZoneInterface $zone */
        $zone = $fixtures['zone_eu'];

        $this->requestDelete(sprintf('/api/v2/admin/zones/%s', $zone->getCode()));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
