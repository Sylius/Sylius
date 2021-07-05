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

use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class LocalesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_locales(): void
    {
        $this->loadFixturesFromFiles(['cart.yaml']);

        $this->client->request('GET', '/api/v2/shop/locales', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_locales_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_locale(): void
    {
        $this->loadFixturesFromFiles(['cart.yaml']);

        $this->client->request('GET', '/api/v2/shop/locales/en_US', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_locale_response', Response::HTTP_OK);
    }
}
