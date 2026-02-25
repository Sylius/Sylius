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

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Sylius\Component\Core\Telemetry\Sender\TelemetrySenderInterface;

/** @internal */
final class TelemetrySender implements TelemetrySenderInterface
{
    private const TIMEOUT = 5;

    public function __construct(
        private ClientInterface $httpClient,
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

            return $response->getStatusCode() === 200;
        } catch (GuzzleException $exception) {
            return false;
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
