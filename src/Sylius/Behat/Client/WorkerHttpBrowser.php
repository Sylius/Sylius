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

namespace Sylius\Behat\Client;

use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\BrowserKit\History;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WorkerHttpBrowser extends HttpBrowser
{
    private ?HttpFoundationResponse $httpFoundationResponse = null;

    private ?BrowserKitResponse $lastBrowserKitResponse = null;

    public function __construct(
        ?HttpClientInterface $client = null,
        ?History $history = null,
        ?CookieJar $cookieJar = null,
        private string $baseUri = 'http://127.0.0.1:8080',
    ) {
        parent::__construct($client, $history, $cookieJar);
    }

    /**
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $files
     * @param array<string, mixed> $server
     */
    public function request(
        string $method,
        string $uri,
        array $parameters = [],
        array $files = [],
        array $server = [],
        ?string $content = null,
        bool $changeHistory = true,
    ): Crawler {
        $port = parse_url($this->baseUri, \PHP_URL_PORT);
        if ($port) {
            if (isset($server['HTTP_HOST']) && is_string($server['HTTP_HOST']) && !str_contains($server['HTTP_HOST'], ':')) {
                $server['HTTP_HOST'] .= ':' . $port;
            }
            if (isset($this->server['HTTP_HOST']) && is_string($this->server['HTTP_HOST']) && !str_contains($this->server['HTTP_HOST'], ':')) {
                $this->server['HTTP_HOST'] .= ':' . $port;
            }
        }

        return parent::request($method, $uri, $parameters, $files, $server, $content, $changeHistory);
    }

    /**
     * @phpstan-ignore method.childReturnType
     */
    public function getResponse(): HttpFoundationResponse
    {
        $response = parent::getResponse();

        if ($this->httpFoundationResponse !== null && $this->lastBrowserKitResponse === $response) {
            return $this->httpFoundationResponse;
        }

        $this->lastBrowserKitResponse = $response;

        return $this->httpFoundationResponse = new HttpFoundationResponse(
            $response->getContent(),
            $response->getStatusCode(),
            $response->getHeaders(),
        );
    }

    protected function getAbsoluteUri(string $uri): string
    {
        if (str_starts_with($uri, '/')) {
            $host = $this->server['HTTP_HOST'] ?? parse_url($this->baseUri, \PHP_URL_HOST);
            $port = parse_url($this->baseUri, \PHP_URL_PORT);
            if ($port && !str_contains($host, ':')) {
                $host .= ':' . $port;
            }

            return sprintf('http://%s%s', $host, $uri);
        }

        return parent::getAbsoluteUri($uri);
    }
}
