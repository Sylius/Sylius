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

namespace Sylius\Bundle\ApiBundle\Tests\Controller;

use ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShippingMethodsTest extends JsonApiTestCase
{
    static private $authorizedHeaderWithDenied = [
        'HTTP_Authorization' => 'Bearer wrong_token',
        'HTTP_ACCEPT' => 'application/ld+json',
        'CONTENT_TYPE' => 'application/ld+json',
    ];

    /**
     * @test
     */
    public function it_denies_to_get_shipping_methods_collection_for_not_authenticated_users(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->getShippingMethodUrl(),
            [],
            [],
            self::$authorizedHeaderWithDenied
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_get_shipping_methods_collection_for_authenticated_users(): void
    {
        $token = $this->logInAsAdminAndGetToken();
        $this->loadFixtures();

        $this->client->request(
            Request::METHOD_GET,
            $this->getShippingMethodUrl(),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token)
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_to_get_shipping_method_for_not_authenticated_users(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->getShippingMethodUrl($this->getShippingMethod($this->loadFixtures(), 'ups')),
            [],
            [],
            self::$authorizedHeaderWithDenied
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_get_shipping_method_for_authenticated_users(): void
    {
        $token = $this->logInAsAdminAndGetToken();

        $this->client->request(
            Request::METHOD_GET,
            $this->getShippingMethodUrl($this->getShippingMethod($this->loadFixtures(), 'ups')),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token)
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_to_post_shipping_method_for_not_authenticated_users(): void
    {
        $data =
<<<EOT
        {
            "zone": "\/new-api\/zones\/EU",
            "code": "UPS",
            "position": 0,
            "calculator": "flat_rate",
            "enabled": true,
            "name": "InPost"
        }
EOT;

        $this->client->request(
            Request::METHOD_POST,
            $this->getShippingMethodUrl(),
            [],
            [],
            self::$authorizedHeaderWithDenied, $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_post_shipping_method_for_authenticated_users(): void
    {
        $this->loadFixtures();
        $token = $this->logInAsAdminAndGetToken();

        $data =
<<<EOT
        {
            "zone": "\/new-api\/zones\/EU",
            "code": "InPost",
            "position": 0,
            "calculator": "flat_rate",
            "enabled": true,
            "name": "InPost"
        }
EOT;

        $this->client->request(
            Request::METHOD_POST,
            $this->getShippingMethodUrl(),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token),
            $data
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_to_put_shipping_method_for_not_authenticated_users(): void
    {
        $data =
<<<EOT
        {
            "name": "InPost"
        }
EOT;

        $this->client->request(
            Request::METHOD_PUT,
            $this->getShippingMethodUrl($this->getShippingMethod($this->loadFixtures(), 'ups')),
            [],
            [],
            self::$authorizedHeaderWithDenied,
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_put_shipping_method_for_authenticated_users(): void
    {
        $token = $this->logInAsAdminAndGetToken();

        $data =
<<<EOT
        {
            "name": "InPost"
        }
EOT;

        $this->client->request(
            Request::METHOD_PUT,
            $this->getShippingMethodUrl($this->getShippingMethod($this->loadFixtures(), 'ups')),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token),
            $data
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_to_delete_shipping_method_for_not_authenticated_users(): void
    {
        $this->client->request(
            Request::METHOD_DELETE,
            $this->getShippingMethodUrl($this->getShippingMethod($this->loadFixtures(), 'ups')),
            [],
            [],
            self::$authorizedHeaderWithDenied
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_delete_shipping_method_for_authenticated_users(): void
    {
        $token = $this->logInAsAdminAndGetToken();

        $this->client->request(
            Request::METHOD_DELETE,
            $this->getShippingMethodUrl($this->getShippingMethod($this->loadFixtures(), 'ups')),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token)
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_denies_to_archive_shipping_method_for_not_authenticated_users(): void
    {
        $this->client->request(
            Request::METHOD_PATCH,
            $this->getShippingMethodUrl(
                $this->getShippingMethod($this->loadFixtures(), 'ups'),
                '/archive'
            ),
            [],
            [],
            self::$authorizedHeaderWithDenied
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_archive_shipping_method_for_authenticated_users(): void
    {
        $token = $this->logInAsAdminAndGetToken();

        $this->client->request(
            Request::METHOD_PATCH,
            $this->getShippingMethodUrl(
                $this->getShippingMethod($this->loadFixtures(), 'ups'),
                '/archive'
            ),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token)
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_to_restore_shipping_method_for_not_authenticated_users(): void
    {
        $this->client->request(
            Request::METHOD_PATCH,
            $this->getShippingMethodUrl(
                $this->getShippingMethod($this->loadFixtures(), 'ups'),
                '/restore'
            ),
            [],
            [],
            self::$authorizedHeaderWithDenied
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_restore_shipping_method_for_authenticated_users(): void
    {
        $token = $this->logInAsAdminAndGetToken();

        $this->client->request(
            Request::METHOD_PATCH,
            $this->getShippingMethodUrl(
                $this->getShippingMethod($this->loadFixtures(), 'ups'),
                '/restore'
            ),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token)
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
    }

    private function getAuthorizedHeaderWithAccept(string $token): array
    {
        return [
            'HTTP_Authorization' => 'Bearer ' . $token,
            'HTTP_ACCEPT' => 'application/ld+json',
            'CONTENT_TYPE' => 'application/ld+json',
        ];
    }

    private function getShippingMethodUrl(
        ?ShippingMethodInterface $shippingMethod = null,
        ?string $suffix = null
    ): string {
        $url = '/new-api/shipping-methods';

        return $shippingMethod !== null ? sprintf('%s/%s%s', $url, $shippingMethod->getCode(), $suffix) : $url;
    }

    private function loadFixtures(): array
    {
        return $this->loadFixturesFromFiles([
            'resources/zones.yml',
            'resources/shipping_methods.yml',
        ]);
    }

    private function getShippingMethod(array $fixtures, string $shippingMethodName): ShippingMethodInterface
    {
        return $fixtures[$shippingMethodName];
    }

    private function logInAsAdminAndGetToken(): string
    {
        /** @var array $fixtures */
        $fixtures = $this->loadFixturesFromFile('authentication/api.yml');

        /** @var AdminUserInterface $admin */
        $admin = $fixtures['admin'];

        $this->client->request(
            'POST',
            '/new-api/admin-user-authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode(['email' => $admin->getEmail(), 'password' => 'sylius-api'])
        );

        return json_decode($this->client->getResponse()->getContent(), true)['token'];
    }
}
