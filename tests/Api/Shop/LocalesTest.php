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

use Sylius\Tests\Api\JsonApiTestCase;

final class LocalesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_locales(): void
    {
        $this->loadFixturesFromFiles(['channel/channel_without_locales.yaml', 'locale.yaml']);

        $this->requestGet(uri: '/api/v2/shop/locales', headers: self::CONTENT_TYPE_HEADER);

        $this->assertResponse($this->client->getResponse(), 'shop/locale/get_locales_response');
    }

    /** @test */
    public function it_gets_only_locales_from_current_channel(): void
    {
        $this->loadFixturesFromFiles(['locale.yaml', 'channel/channel.yaml']);

        $this->requestGet(uri: '/api/v2/shop/locales', headers: self::CONTENT_TYPE_HEADER);

        $this->assertResponse($this->client->getResponse(), 'shop/locale/get_locales_from_channel_response');
    }

    /** @test */
    public function it_gets_locale(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml']);

        $this->requestGet(uri: '/api/v2/shop/locales/en_US', headers: self::CONTENT_TYPE_HEADER);

        $this->assertResponse($this->client->getResponse(), 'shop/locale/get_locale_response');
    }
}
