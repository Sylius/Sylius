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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ChannelsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_creates_a_channel(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'currency.yaml', 'locale.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/channels',
            [],
            [],
            $header,
            json_encode([
                'name' => 'Web Store',
                'code' => 'WEB',
                'baseCurrency' => '/api/v2/admin/currencies/USD',
                'defaultLocale' => '/api/v2/admin/locales/en_US',
                'taxCalculationStrategy' => 'order_items_based',
                'shippingAddressInCheckoutRequired' => true,

            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/post_channel_response',
            Response::HTTP_CREATED
        );
    }

    /** @test */
    public function it_updates_an_existing_channel(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);

        /** @var ChannelInterface $channel */
        $channel = $fixtures['channel_web'];

        $header = $this->getLoggedHeader();

        $this->client->request(
            'PUT',
            '/api/v2/admin/channels/' . $channel->getCode(),
            [],
            [],
            $header,
            json_encode([
                'shippingAddressInCheckoutRequired' => true,
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/put_channel_response',
            Response::HTTP_OK
        );
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
