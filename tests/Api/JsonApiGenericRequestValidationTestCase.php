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

namespace Sylius\Tests\Api;

use Symfony\Component\HttpFoundation\Response;

final class JsonApiGenericRequestValidationTestCase extends JsonApiTestCase
{
    /** @test */
    public function it_returns_a_bad_request_response_code_if_request_body_is_not_valid_json(): void
    {
        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/orders',
            server: self::CONTENT_TYPE_HEADER,
            content: 'Malformed JSON: the provided JSON payload is not properly formatted.',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'malformed_json_response',
            Response::HTTP_BAD_REQUEST,
        );
    }
}
