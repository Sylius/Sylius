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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Sylius\Component\Core\Telemetry\Sender\TelemetrySenderInterface;

/** @internal */
final class TelemetrySender implements TelemetrySenderInterface
{
    private const TIMEOUT = 5;

    /** @var string */
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /** @param array<string, mixed> $telemetryData */
    public function send(array $telemetryData): bool
    {
        try {
            $client = new Client([
                'timeout' => self::TIMEOUT,
                'verify' => false,
            ]);

            $response = $client->request('POST', $this->url, [
                'json' => $telemetryData,
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
