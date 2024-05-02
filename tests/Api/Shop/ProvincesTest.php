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

namespace Api\Shop;

use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProvincesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_a_province(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['country.yaml']);

        /** @var ProvinceInterface $province */
        $province = $fixtures['province_US_WY'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/provinces/%s', $province->getCode()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/province/get_province_response',
            Response::HTTP_OK,
        );
    }
}
