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

namespace Tests\Sylius\Bundle\CoreBundle\Telemetry\Sender;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\Sender\TelemetrySender;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class TelemetrySenderTest extends TestCase
{
    public function test_it_sends_telemetry_data_successfully(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getStatusCode')->willReturn(200);

        $httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'http://localhost:8000/telemetry',
                $this->callback(function ($options) {
                    return isset($options['json'])
                        && isset($options['timeout'])
                        && $options['timeout'] === 5
                        && isset($options['headers']['Content-Type'])
                        && $options['headers']['Content-Type'] === 'application/json'
                        && isset($options['headers']['User-Agent'])
                        && $options['headers']['User-Agent'] === 'Sylius-Prism/1.0';
                }),
            )
            ->willReturn($response);

        $sender = new TelemetrySender($httpClient, 'http://localhost:8000/telemetry');

        $result = $sender->send(['test' => 'data']);

        self::assertTrue($result);
    }

    public function test_it_returns_false_on_http_error(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getStatusCode')->willReturn(500);

        $httpClient
            ->method('request')
            ->willReturn($response);

        $sender = new TelemetrySender($httpClient, 'http://localhost:8000/telemetry');

        $result = $sender->send(['test' => 'data']);

        self::assertFalse($result);
    }

    public function test_it_returns_false_on_transport_exception(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);

        $httpClient
            ->method('request')
            ->willThrowException(new \RuntimeException('Network error'));

        $sender = new TelemetrySender($httpClient, 'http://localhost:8000/telemetry');

        $result = $sender->send(['test' => 'data']);

        self::assertFalse($result);
    }

    public function test_it_considers_201_status_code_as_failure(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getStatusCode')->willReturn(201);

        $httpClient->method('request')->willReturn($response);

        $sender = new TelemetrySender($httpClient, 'http://localhost:8000/telemetry');

        $result = $sender->send(['test' => 'data']);

        self::assertFalse($result);
    }

    public function test_it_considers_3xx_status_codes_as_failure(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getStatusCode')->willReturn(302);

        $httpClient->method('request')->willReturn($response);

        $sender = new TelemetrySender($httpClient, 'http://localhost:8000/telemetry');

        $result = $sender->send(['test' => 'data']);

        self::assertFalse($result);
    }

    public function test_it_considers_4xx_status_codes_as_failure(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getStatusCode')->willReturn(404);

        $httpClient->method('request')->willReturn($response);

        $sender = new TelemetrySender($httpClient, 'http://localhost:8000/telemetry');

        $result = $sender->send(['test' => 'data']);

        self::assertFalse($result);
    }
}
