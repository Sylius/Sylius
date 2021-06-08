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

final class GetCountriesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_countries(): void
    {
        $this->loadFixturesFromFiles(['country.yaml']);

        $this->client->request('GET', '/api/v2/shop/countries', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_countries_response', Response::HTTP_OK);
    }
}
