<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\Api\Shop;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class AddressesPostTest extends JsonApiTestCase
{
    /** @test */
    public function it_denies_access_to_a_create_an_address_for_not_authenticated_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CountryInterface $country */
        $country = $fixtures['country_DE'];

        $bodyRequest = $this->createBodyRequest($country->getCode());

        $this->client->request(
            'POST',
            '/api/v2/shop/addresses',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode($bodyRequest)
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_creates_new_address_for_logged_customer_with_country_with_provinces(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        /** @var CountryInterface $country */
        $country = $fixtures['country_US'];
        /** @var ProvinceInterface $province */
        $province = $fixtures['province_US_MI'];

        $authorizationHeader = $this->getAuthorizationHeaderAsCustomer($customer->getEmailCanonical(), 'sylius');

        $bodyRequest = $this->createBodyRequest($country->getCode(), $province->getCode());

        $this->client->request(
            'POST',
            '/api/v2/shop/addresses',
            [],
            [],
            array_merge($authorizationHeader, self::CONTENT_TYPE_HEADER),
            json_encode($bodyRequest)
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/address/create_address_with_province_code_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_creates_new_address_for_logged_customer_without_province(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        /** @var CountryInterface $country */
        $country = $fixtures['country_DE'];

        $authorizationHeader = $this->getAuthorizationHeaderAsCustomer($customer->getEmailCanonical(), 'sylius');

        $bodyRequest = $this->createBodyRequest($country->getCode(), provinceName: 'Munich');

        $this->client->request(
            'POST',
            '/api/v2/shop/addresses',
            [],
            [],
            array_merge($authorizationHeader, self::CONTENT_TYPE_HEADER),
            json_encode($bodyRequest)
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/address/create_address_with_province_name_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_creates_new_address_for_logged_customer_with_country_with_custom_provinces(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        /** @var CountryInterface $country */
        $country = $fixtures['country_DE'];

        $authorizationHeader = $this->getAuthorizationHeaderAsCustomer($customer->getEmailCanonical(), 'sylius');

        $bodyRequest = $this->createBodyRequest($country->getCode());

        $this->client->request(
            'POST',
            '/api/v2/shop/addresses',
            [],
            [],
            array_merge($authorizationHeader, self::CONTENT_TYPE_HEADER),
            json_encode($bodyRequest)
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/address/create_address_without_province_response', Response::HTTP_CREATED);
    }

    private function createBodyRequest(
        string $countryCode,
        ?string $provinceCode = null,
        ?string $provinceName = null
    ): array {
        return [
            'firstName' => 'TEST',
            'lastName' => 'TEST',
            'phoneNumber' => '666111333',
            'company' => 'Potato Corp.',
            'countryCode' => $countryCode,
            'provinceCode' => $provinceCode,
            'provinceName' => $provinceName,
            'street' => 'Top secret',
            'city' => 'Nebraska',
            'postcode' => '12343',
        ];
    }
}
