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

namespace Sylius\Tests\Controller;

use ApiTestCase\JsonApiTestCase;

final class XFrameOptionsTest extends JsonApiTestCase
{
    /** @test */
    public function it_sets_frame_options_header(): void
    {
        $this->client->request('GET', '/');

        $response = $this->client->getResponse();

        $this->assertSame('sameorigin', $response->headers->get('X-Frame-Options'));
    }
}
