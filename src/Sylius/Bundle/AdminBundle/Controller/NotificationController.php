<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Controller;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use Http\Message\MessageFactory;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\UriInterface;
use Sylius\Bundle\CoreBundle\Application\Kernel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NotificationController
{
    private const CACHE_KEY = 'sylius_admin_notification_version';

    private const TTL = 86400;

    /** @var ClientInterface */
    private $client;

    /** @var MessageFactory */
    private $messageFactory;

    /** @var UriInterface */
    private $hubUri;

    /** @var string */
    private $environment;

    /** @var CacheItemPoolInterface */
    private $cache;

    public function __construct(
        ClientInterface $client,
        MessageFactory $messageFactory,
        string $hubUri,
        string $environment,
        CacheItemPoolInterface $cache
    ) {
        $this->client = $client;
        $this->messageFactory = $messageFactory;
        $this->hubUri = new Uri($hubUri);
        $this->environment = $environment;
        $this->cache = $cache;
    }

    public function getVersionAction(Request $request): JsonResponse
    {
        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if ($cacheItem->isHit()) {
            $cached = $cacheItem->get();
            if (null === $cached || '' === $cached) {
                return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
            }

            return new JsonResponse($cached);
        }

        $content = [
            'version' => Kernel::VERSION,
            'hostname' => $request->getHost(),
            'locale' => $request->getLocale(),
            'user_agent' => $request->headers->get('User-Agent'),
            'environment' => $this->environment,
        ];

        $headers = ['Content-Type' => 'application/json'];

        $hubRequest = $this->messageFactory->createRequest(
            Request::METHOD_GET,
            $this->hubUri,
            $headers,
            json_encode($content)
        );

        try {
            $hubResponse = $this->client->send($hubRequest, [
                'verify' => false,
                'timeout' => 2,
                'connect_timeout' => 1,
            ]);
        } catch (GuzzleException $exception) {
            $this->saveToCache($cacheItem, '');

            return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
        }

        $hubResponse = json_decode($hubResponse->getBody()->getContents(), true);

        $this->saveToCache($cacheItem, $hubResponse);

        return new JsonResponse($hubResponse);
    }

    private function saveToCache(CacheItemInterface $cacheItem, $data): void
    {
        $cacheItem->set($data);
        $cacheItem->expiresAfter(self::TTL);

        $this->cache->save($cacheItem);
    }
}
