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

use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Tests\Api\JsonApiTestCase;

final class ProvincesTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDefaultGetHeaders();
    }

    /** @test */
    public function it_gets_a_province(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['country.yaml']);

        /** @var ProvinceInterface $province */
        $province = $fixtures['province_US_WY'];

        $this->requestGet(
            sprintf('/api/v2/shop/countries/%s/provinces/%s', $province->getCountry()->getCode(), $province->getCode()),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/province/get_province_response');
    }
}
