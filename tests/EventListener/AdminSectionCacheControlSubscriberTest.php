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

namespace Sylius\Tests\EventListener;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AdminSectionCacheControlSubscriberTest extends WebTestCase
{
    /**
     * @test
     */
    public function it_returns_proper_cache_headers_for_admin_endpoints(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin/');

        $this->assertResponseHeaderSame('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store, private');
    }
}
