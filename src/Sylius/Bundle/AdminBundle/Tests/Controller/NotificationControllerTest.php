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

namespace Sylius\Bundle\AdminBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Sylius\Bundle\AdminBundle\Controller\NotificationController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NotificationControllerTest extends TestCase
{
    /** @var ObjectProphecy|ClientInterface */
    private $client;

    /** @var ObjectProphecy|RequestFactoryInterface */
    private $requestFactory;

    /** @var ObjectProphecy|StreamFactoryInterface */
    private $streamFactory;

    /** @var ObjectProphecy|CacheItemPoolInterface */
    private $cache;

    /** @var ObjectProphecy|CacheItemInterface */
    private $cacheItem;

    /** @var NotificationController */
    private $controller;

    /** @var string */
    private static $hubUri = 'www.doesnotexist.test.com';

    /**
     * @test
     */
    public function it_returns_cached_response_when_cache_is_hit(): void
    {
        $cachedData = ['version' => '9001'];

        $this->cache->getItem('sylius_admin_notification_version')->willReturn($this->cacheItem->reveal());
        $this->cacheItem->isHit()->willReturn(true);
        $this->cacheItem->get()->willReturn($cachedData);

        $this->client->sendRequest(Argument::cetera())->shouldNotBeCalled();
        $this->requestFactory->createRequest(Argument::cetera())->shouldNotBeCalled();

        $response = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($cachedData), $response->getContent());
        $this->cache->save(Argument::any())->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_returns_no_content_when_cached_value_is_empty(): void
    {
        $this->cacheItem->isHit()->willReturn(true);
        $this->cacheItem->get()->willReturn('');

        $this->client->sendRequest(Argument::cetera())->shouldNotBeCalled();
        $this->requestFactory->createRequest(Argument::cetera())->shouldNotBeCalled();

        $response = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->cache->save(Argument::any())->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_returns_no_content_when_cached_value_is_null(): void
    {
        $this->cacheItem->isHit()->willReturn(true);
        $this->cacheItem->get()->willReturn(null);

        $this->client->sendRequest(Argument::cetera())->shouldNotBeCalled();
        $this->requestFactory->createRequest(Argument::cetera())->shouldNotBeCalled();

        $response = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->cache->save(Argument::any())->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_returns_an_empty_json_response_upon_client_exception(): void
    {
        $this->stubRequestFactory();

        $this->client->sendRequest(Argument::any())
            ->willThrow(new class() extends \RuntimeException implements ClientExceptionInterface {
            });

        $emptyResponse = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $emptyResponse->getStatusCode());
        $this->assertEquals('""', $emptyResponse->getContent());
        $this->cacheItem->set('')->shouldHaveBeenCalled();
        $this->cacheItem->expiresAfter(86400)->shouldHaveBeenCalled();
        $this->cache->save($this->cacheItem->reveal())->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_returns_json_response_from_client_on_success(): void
    {
        $content = json_encode(['version' => '9001']);

        $this->stubRequestFactory();

        /** @var ObjectProphecy|StreamInterface $stream */
        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn($content);

        /** @var ObjectProphecy|ResponseInterface $externalResponse */
        $externalResponse = $this->prophesize(ResponseInterface::class);
        $externalResponse->getBody()->willReturn($stream->reveal());

        $this->client->sendRequest(Argument::any())->willReturn($externalResponse->reveal());

        $response = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($content, $response->getContent());
        $this->cacheItem->set(['version' => '9001'])->shouldHaveBeenCalled();
        $this->cacheItem->expiresAfter(86400)->shouldHaveBeenCalled();
        $this->cache->save($this->cacheItem->reveal())->shouldHaveBeenCalled();
    }

    protected function setUp(): void
    {
        $this->client = $this->prophesize(ClientInterface::class);
        $this->requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $this->streamFactory = $this->prophesize(StreamFactoryInterface::class);
        $this->cache = $this->prophesize(CacheItemPoolInterface::class);
        $this->cacheItem = $this->prophesize(CacheItemInterface::class);

        $this->cache->getItem('sylius_admin_notification_version')->willReturn($this->cacheItem->reveal());
        $this->cacheItem->isHit()->willReturn(false);
        $this->cacheItem->set(Argument::any())->willReturn($this->cacheItem->reveal());
        $this->cacheItem->expiresAfter(Argument::any())->willReturn($this->cacheItem->reveal());
        $this->cache->save(Argument::any())->willReturn(true);

        $this->controller = new NotificationController(
            $this->client->reveal(),
            $this->requestFactory->reveal(),
            self::$hubUri,
            'environment',
            $this->cache->reveal(),
            $this->streamFactory->reveal(),
        );

        parent::setUp();
    }

    private function stubRequestFactory(): void
    {
        $hubRequest = $this->prophesize(RequestInterface::class);
        $hubRequest->withHeader('Content-Type', 'application/json')->willReturn($hubRequest->reveal());
        $hubRequest->withBody(Argument::any())->willReturn($hubRequest->reveal());

        $this->requestFactory->createRequest(Request::METHOD_GET, self::$hubUri)
            ->willReturn($hubRequest->reveal());

        $this->streamFactory->createStream(Argument::any())
            ->willReturn($this->prophesize(StreamInterface::class)->reveal());
    }
}
