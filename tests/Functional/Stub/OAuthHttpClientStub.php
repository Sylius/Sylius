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

namespace Sylius\Tests\Functional\Stub;

use LogicException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Stands in for "hwi_oauth.http_client" so that the real resource owners can be driven without leaving the test.
 *
 * Replacing the resource owner itself is not an option: ResourceOwnerMap resolves it from a compiled ServiceLocator,
 * which a container override does not reach. Swapping the HTTP client underneath keeps the genuine resource owner in
 * play, including the way it parses the payload and maps it onto a UserResponseInterface.
 */
final class OAuthHttpClientStub extends MockHttpClient
{
    /** @var array<string, array<string, mixed>> */
    private array $payloads = [];

    public function __construct()
    {
        parent::__construct(fn (string $method, string $url): MockResponse => $this->createResponse($url));
    }

    /** @param array<string, mixed> $payload */
    public function willRespondTo(string $urlFragment, array $payload): void
    {
        $this->payloads[$urlFragment] = $payload;
    }

    private function createResponse(string $url): MockResponse
    {
        foreach ($this->payloads as $urlFragment => $payload) {
            if (str_contains($url, $urlFragment)) {
                return new MockResponse(
                    json_encode($payload, \JSON_THROW_ON_ERROR),
                    ['response_headers' => ['content-type' => 'application/json']],
                );
            }
        }

        throw new LogicException(sprintf('No canned response was registered for the "%s" URL.', $url));
    }
}
