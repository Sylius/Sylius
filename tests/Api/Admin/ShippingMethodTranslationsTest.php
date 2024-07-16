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

namespace Api\Admin;

use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ShippingMethodTranslationsTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();

        parent::setUp();
    }

    /** @test */
    public function it_returns_not_found_on_get_shipping_method_translation(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        $this->requestGet('/api/v2/admin/shipping-methods/UPS/translations/en_US');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
