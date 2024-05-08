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

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class AddressesTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /** @test */
    public function it_denies_access_to_get_addresses_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFiles(['authentication/customer.yaml']);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/addresses', server: self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_addresses(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['address_with_customer.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/addresses', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/address/get_addresses_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_an_address(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['address_with_customer.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];
        /** @var AddressInterface $address */
        $address = $fixtures['address'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/addresses/' . $address->getId(), server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/address/get_address_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_denies_access_to_create_an_address_for_not_authenticated_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CountryInterface $country */
        $country = $fixtures['country_DE'];

        $bodyRequest = $this->createBodyRequest($country->getCode());

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/addresses',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode($bodyRequest, \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_creates_a_new_address_with_country_and_province_code(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        /** @var CountryInterface $country */
        $country = $fixtures['country_US'];
        /** @var ProvinceInterface $province */
        $province = $fixtures['province_US_MI'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $bodyRequest = $this->createBodyRequest($country->getCode(), $province->getCode());

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/addresses',
            server: $header,
            content: json_encode($bodyRequest, \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/address/post_address_with_province_code_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_a_new_address_with_country_and_province_code_when_the_country_code_is_set_after_province_code_in_body(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        /** @var CountryInterface $country */
        $country = $fixtures['country_US'];
        /** @var ProvinceInterface $province */
        $province = $fixtures['province_US_MI'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/addresses',
            server: $header,
            content: json_encode([
                'firstName' => 'TEST',
                'lastName' => 'TEST',
                'phoneNumber' => '666111333',
                'company' => 'Potato Corp.',
                'provinceCode' => $province->getCode(),
                'countryCode' => $country->getCode(),
                'street' => 'Top secret',
                'city' => 'Nebraska',
                'postcode' => '12343',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/address/post_address_with_province_code_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_a_new_address_with_country_and_province_name(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        /** @var CountryInterface $country */
        $country = $fixtures['country_DE'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $bodyRequest = $this->createBodyRequest($country->getCode(), provinceName: 'Munich');

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/addresses',
            server: $header,
            content: json_encode($bodyRequest, \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/address/post_address_with_province_name_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_a_new_address_with_country_without_province_data(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        /** @var CountryInterface $country */
        $country = $fixtures['country_DE'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $bodyRequest = $this->createBodyRequest($country->getCode());

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/addresses',
            server: $header,
            content: json_encode($bodyRequest, \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/address/post_address_without_province_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_create_a_new_address_with_invalid_data(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/addresses',
            server: $header,
            content: json_encode([
                'firstName' => 'Tony',
                'lastName' => 'Stark',
                'company' => str_repeat('1', 256),
                'countryCode' => 'INVALID_COUNTRY_CODE',
                'street' => '10880 Malibu Point',
                'city' => 'Malibu',
                'postcode' => '90265',
                'phoneNumber' => str_repeat('1', 256),
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
    public function it_updates_an_address(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['address_with_customer.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];
        /** @var AddressInterface $address */
        $address = $fixtures['address'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/shop/addresses/' . $address->getId(),
            server: $header,
            content: json_encode([
                'firstName' => 'Tony',
                'lastName' => 'Stark',
                'company' => 'Stark Industries',
                'countryCode' => 'US',
                'provinceCode' => 'US-WY',
                'street' => '10880 Malibu Point',
                'city' => 'Malibu',
                'postcode' => '90265',
                'phoneNumber' => '123456789',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/address/put_address_response',
            Response::HTTP_OK,
        );
    }

    private function createBodyRequest(
        string $countryCode,
        ?string $provinceCode = null,
        ?string $provinceName = null,
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
