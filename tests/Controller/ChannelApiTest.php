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

namespace Sylius\Tests\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Response;

final class ChannelApiTest extends JsonApiTestCase
{
    /** @var array */
    private static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    /** @var array */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_does_not_allow_to_show_channels_list_when_access_is_denied(): void
    {
        $this->loadFixturesFromFile('resources/channels.yml');

        $this->client->request('GET', '/api/v1/channels/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_denies_creating_channels_for_non_authenticated_user(): void
    {
        $this->client->request('POST', '/api/v1/channels/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_channel_without_specifying_required_data(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/channels/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_indexing_channels(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');

        $this->client->request('GET', '/api/v1/channels/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_getting_channel_for_non_authenticated_user(): void
    {
        $this->client->request('GET', '/api/v1/channels/none');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_channel_when_it_does_not_exist(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/channels/none', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_create_channel_with_required_data(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $this->loadFixturesFromFile('resources/currencies.yml');

        $data =
            <<<EOT
        {
            "code": "mob",
            "name": "Channel for mobile",
            "taxCalculationStrategy": "order_items_based",
            "baseCurrency": "USD",
            "defaultLocale": "en_US",
            "enabled": true
        }
EOT;

        $this->client->request('POST', '/api/v1/channels/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_create_channel_with_extra_data(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $this->loadFixturesFromFile('resources/currencies.yml');
        $this->loadFixturesFromFile('resources/zones.yml');
        $this->loadFixturesFromFile('resources/taxons.yml');

        $data =
<<<EOT
        {
            "code": "android",
            "name": "Channel for Android client",
            "taxCalculationStrategy": "order_items_based",
            "baseCurrency": "EUR",
            "defaultLocale": "en_US",
            "enabled": true,
            "description": "For now not required, for future development stages.",
            "defaultTaxZone": "EU",
            "hostname": "quickmart.eu",
            "currencies": [
                "GBP"
            ],
            "menuTaxon": "mugs",
            "color": "ClassicBlue",
            "contactEmail": "admin@quickmart.eu",
            "skippingShippingStepAllowed": true,
            "skippingPaymentStepAllowed": true,
            "accountVerificationRequired": false
        }
EOT;

        $this->client->request('POST', '/api/v1/channels/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/create_with_extra_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_updating_channel_for_non_authenticated_user(): void
    {
        $this->client->request('PUT', '/api/v1/channels/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_channel_without_specifying_required_data(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');

        /** @var ChannelInterface $channel */
        $channel = $channels['channel_web'];

        $this->client->request('PUT', $this->getChannelUrl($channel), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_update_channel(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');

        /** @var ChannelInterface $channel */
        $channel = $channels['channel_web'];

        $data =
            <<<EOT
        {
            "color": "black",
            "enabled": false,
            "description": "Lorem ipsum",
            "name": "Web Channel",
            "hostname": "localhost",
            "taxCalculationStrategy": "order_items_based"
        }
EOT;

        $this->client->request('PUT', $this->getChannelUrl($channel), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getChannelUrl($channel), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_showing_channel(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');
        /** @var ChannelInterface $channel */
        $channel = $channels['channel_web'];

        $this->client->request('GET', $this->getChannelUrl($channel), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_partially_updating_channel_which_does_not_exist(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/v1/channels/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_partially_update_channel(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');

        /** @var ChannelInterface $channel */
        $channel = $channels['channel_web'];

        $data =
            <<<EOT
        {
            "color": "black",
            "enabled": false
        }
EOT;

        $this->client->request('PATCH', $this->getChannelUrl($channel), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getChannelUrl($channel), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_delete_non_existing_channel(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/channels/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_channel(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');
        $channel = $channels['channel-mob'];

        $this->client->request('DELETE', $this->getChannelUrl($channel), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getChannelUrl($channel), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    private function getChannelUrl(ChannelInterface $channel): string
    {
        return '/api/v1/channels/' . $channel->getCode();
    }
}
