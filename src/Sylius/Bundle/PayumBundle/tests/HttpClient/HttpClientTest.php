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

namespace Tests\Sylius\Bundle\PayumBundle\HttpClient;

use Payum\Core\HttpClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sylius\Bundle\PayumBundle\HttpClient\HttpClient;

final class HttpClientTest extends TestCase
{
    private ClientInterface&MockObject $client;

    private HttpClient $httpClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->createMock(ClientInterface::class);
        $this->httpClient = new HttpClient($this->client);
    }

    public function testImplementsHttpClientInterface(): void
    {
        self::assertInstanceOf(HttpClientInterface::class, $this->httpClient);
    }

    public function testSendsARequest(): void
    {
        /** @var RequestInterface&MockObject $request */
        $request = $this->createMock(RequestInterface::class);
        /** @var ResponseInterface&MockObject $response */
        $response = $this->createMock(ResponseInterface::class);

        $this->client->expects(self::once())
            ->method('sendRequest')
            ->with($request)->willReturn($response);

        self::assertSame($response, $this->httpClient->send($request));
    }
}
