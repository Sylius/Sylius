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

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class AddressesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_address(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'address_with_customer.yaml']);

        /** @var AddressInterface $address */
        $address = $fixtures['address'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/addresses/%d', $address->getId()),
            server: $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/address/get_address',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_updates_address(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'address_with_customer.yaml']);

        /** @var AddressInterface $address */
        $address = $fixtures['address'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/addresses/%d', $address->getId()),
            server: $this->headerBuilder()->withJsonLdContentType()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
            content: json_encode([
                'firstName' => 'Finley',
                'lastName' => 'Ward',
                'company' => 'Company',
                'countryCode' => 'GB',
                'street' => 'New Henry St',
                'city' => 'Neath',
                'postcode' => 'SA11 1PH',
                'phoneNumber' => '01639 644902',
                'provinceName' => 'West Glamorgan',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/address/put_address',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_updates_address_with_invalid_data(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'address_with_customer.yaml']);

        /** @var AddressInterface $address */
        $address = $fixtures['address'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/addresses/%d', $address->getId()),
            server: $this->headerBuilder()->withJsonLdContentType()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
            content: json_encode([
                'firstName' => 'Finley',
                'lastName' => 'Ward',
                'company' => str_repeat('1', 256),
                'countryCode' => 'INVALID_COUNTRY_CODE',
                'street' => 'New Henry St',
                'city' => 'Neath',
                'postcode' => 'SA11 1PH',
                'phoneNumber' => str_repeat('1', 256),
                'provinceName' => 'West Glamorgan',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonResponseViolations($this->client->getResponse(), [
            [
                'propertyPath' => 'countryCode',
                'message' => 'This value is not a valid country.',
            ],
            [
                'propertyPath' => 'phoneNumber',
                'message' => 'This value is too long. It should have 255 characters or less.',
            ],
            [
                'propertyPath' => 'company',
                'message' => 'This value is too long. It should have 255 characters or less.',
            ],
        ]);
    }

    /** @test */
    public function it_gets_address_log_entries(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'address_with_customer.yaml']);

        /** @var AddressInterface $address */
        $address = $fixtures['address'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/addresses/%d/log-entries', $address->getId()),
            server: $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/address/get_address_log_entries',
            Response::HTTP_OK,
        );
    }
}
