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

namespace Sylius\Bundle\CoreBundle\Telemetry\Sender;

use Sylius\Component\Core\Telemetry\Sender\TelemetrySenderInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/** @internal */
final class TelemetrySender implements TelemetrySenderInterface
{
    private const TIMEOUT = 5;

    public function __construct(
        private HttpClientInterface $httpClient,
        private string $url,
    ) {
    }

    /** @param array<string, mixed> $telemetryData */
    public function send(array $telemetryData): bool
    {
        try {
            $response = $this->httpClient->request('POST', $this->url, [
                'json' => $telemetryData,
                'timeout' => self::TIMEOUT,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Sylius-Prism/1.0',
                ],
            ]);

            $statusCode = $response->getStatusCode();

            return $statusCode === 200;
        } catch (TransportExceptionInterface $exception) {
            return false;
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
