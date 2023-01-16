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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ChannelsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_channel(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ChannelInterface $channel */
        $channel = $fixtures['channel_web'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/channels/%s', $channel->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/channel/get_channel_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_channels(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/channels',
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/channel/get_channels_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_channel(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'currency.yaml', 'locale.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/channels',
            server: $header,
            content: json_encode([
                'name' => 'Web Store',
                'code' => 'WEB',
                'description' => 'Lorem ipsum',
                'hostname' => 'test.com',
                'color' => 'blue',
                'enabled' => true,
                'baseCurrency' => '/api/v2/admin/currencies/USD',
                'defaultLocale' => '/api/v2/admin/locales/en_US',
                'taxCalculationStrategy' => 'order_items_based',
                'currencies' => [],
                'locales' => [],
                'themeName' => 'garish',
                'contactEmail' => 'contact@test.com',
                'contactPhoneNumber' => '1-800-00-00-00',
                'skippingShippingStepAllowed' => false,
                'skippingPaymentStepAllowed' => true,
                'accountVerificationRequired' => true,
                'shippingAddressInCheckoutRequired' => false,
                'menuTaxon' => null,

            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/channel/post_channel_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_an_existing_channel(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);

        /** @var ChannelInterface $channel */
        $channel = $fixtures['channel_web'];

        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/channels/' . $channel->getCode(),
            server: $header,
            content: json_encode([
                'shippingAddressInCheckoutRequired' => true,
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/channel/put_channel_response',
            Response::HTTP_OK,
        );
    }
}
