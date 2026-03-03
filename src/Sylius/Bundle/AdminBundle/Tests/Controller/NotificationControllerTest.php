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

namespace Sylius\Bundle\AdminBundle\Tests\Controller;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use Http\Message\MessageFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ProphecyInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sylius\Bundle\AdminBundle\Controller\NotificationController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NotificationControllerTest extends TestCase
{
    /** @var ProphecyInterface|ClientInterface */
    private $client;

    /** @var ProphecyInterface|MessageFactory */
    private $messageFactory;

    /** @var ProphecyInterface|CacheItemPoolInterface */
    private $cache;

    /** @var ProphecyInterface|CacheItemInterface */
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

        $this->client->send(Argument::cetera())->shouldNotBeCalled();

        $response = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(json_encode($cachedData), $response->getContent());
    }

    /**
     * @test
     */
    public function it_returns_no_content_when_cached_value_is_empty(): void
    {
        $this->cacheItem->isHit()->willReturn(true);
        $this->cacheItem->get()->willReturn('');

        $this->client->send(Argument::cetera())->shouldNotBeCalled();

        $response = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_no_content_when_cached_value_is_null(): void
    {
        $this->cacheItem->isHit()->willReturn(true);
        $this->cacheItem->get()->willReturn(null);

        $this->client->send(Argument::cetera())->shouldNotBeCalled();

        $response = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_an_empty_json_response_upon_client_exception(): void
    {
        $this->messageFactory->createRequest(Argument::any(), Argument::cetera())
            ->willReturn($this->prophesize(RequestInterface::class)->reveal())
        ;

        $this->client->send(Argument::any(), [
            'verify' => false,
            'timeout' => 2,
            'connect_timeout' => 1,
        ])->willThrow(ConnectException::class);

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

        $this->messageFactory->createRequest(Argument::any(), Argument::cetera())
            ->willReturn($this->prophesize(RequestInterface::class)->reveal())
        ;

        /** @var ProphecyInterface|StreamInterface $stream */
        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn($content);

        /** @var ProphecyInterface|ResponseInterface $externalResponse */
        $externalResponse = $this->prophesize(ResponseInterface::class);
        $externalResponse->getBody()->willReturn($stream->reveal());

        $this->client->send(Argument::any(), [
            'verify' => false,
            'timeout' => 2,
            'connect_timeout' => 1,
        ])->willReturn($externalResponse->reveal());

        $response = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($content, $response->getContent());
        $this->cacheItem->expiresAfter(86400)->shouldHaveBeenCalled();
        $this->cache->save($this->cacheItem->reveal())->shouldHaveBeenCalled();
    }

    protected function setUp(): void
    {
        $this->client = $this->prophesize(ClientInterface::class);
        $this->messageFactory = $this->prophesize(MessageFactory::class);
        $this->cache = $this->prophesize(CacheItemPoolInterface::class);
        $this->cacheItem = $this->prophesize(CacheItemInterface::class);

        $this->cache->getItem('sylius_admin_notification_version')->willReturn($this->cacheItem->reveal());
        $this->cacheItem->isHit()->willReturn(false);
        $this->cacheItem->set(Argument::any())->willReturn($this->cacheItem->reveal());
        $this->cacheItem->expiresAfter(Argument::any())->willReturn($this->cacheItem->reveal());
        $this->cache->save(Argument::any())->willReturn(true);

        $this->controller = new NotificationController(
            $this->client->reveal(),
            $this->messageFactory->reveal(),
            self::$hubUri,
            'environment',
            $this->cache->reveal()
        );

        parent::setUp();
    }
}
