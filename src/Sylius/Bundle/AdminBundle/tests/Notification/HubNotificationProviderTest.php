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

namespace Tests\Sylius\Bundle\AdminBundle\Notification;

use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Sylius\Bundle\AdminBundle\Notification\HubNotificationProvider;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;

final class HubNotificationProviderTest extends TestCase
{
    private ClientInterface&MockObject $client;

    private RequestStack&MockObject $requestStack;

    private RequestFactoryInterface&MockObject $requestFactory;

    private StreamFactoryInterface&MockObject $streamFactory;

    private CacheInterface&MockObject $cache;

    private ClockInterface&MockObject $clock;

    private HubNotificationProvider $hubNotificationsProvider;

    private static string $hubUri = 'www.doesnotexist.test.com';

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(ClientInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
        $this->clock = $this->createMock(ClockInterface::class);

        $this->hubNotificationsProvider = new HubNotificationProvider(
            $this->client,
            $this->requestStack,
            $this->requestFactory,
            $this->streamFactory,
            $this->cache,
            $this->clock,
            self::$hubUri,
            'prod',
            true,
            60,
        );
    }

    #[Test]
    public function it_returns_an_empty_array_if_client_exception_occurs(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('latest_sylius_version', $this->isType('callable'))
            ->willReturnCallback(fn ($key, $callback) => $callback());

        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(new Request());

        $this->clock
            ->expects($this->once())
            ->method('now')
            ->willReturn(new \DateTimeImmutable());

        $this->streamFactory
            ->expects($this->once())
            ->method('createStream')
            ->willReturn($stream);

        $this->requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->willReturn($request);

        $request
            ->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturn($request);

        $request
            ->expects($this->once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($request);

        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->willThrowException(new ConnectException('Connection failed', $this->createMock(\Psr\Http\Message\RequestInterface::class)));

        $this->assertEmpty($this->hubNotificationsProvider->getNotifications());
    }

    #[Test]
    public function it_returns_an_empty_array_if_the_current_version_is_the_same_as_latest(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $stream = $this->createMock(StreamInterface::class);
        $externalResponse = $this->createMock(ResponseInterface::class);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('latest_sylius_version', $this->isType('callable'))
            ->willReturnCallback(fn ($key, $callback) => $callback());

        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(new Request());

        $this->clock
            ->expects($this->once())
            ->method('now')
            ->willReturn(new \DateTimeImmutable());

        $this->streamFactory
            ->expects($this->once())
            ->method('createStream')
            ->willReturn($stream);

        $content = json_encode(['version' => SyliusCoreBundle::VERSION]);
        $stream
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($content);

        $this->requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->willReturn($request);

        $request
            ->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturn($request);

        $request
            ->expects($this->once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($request);

        $externalResponse
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($externalResponse);

        $this->assertEmpty($this->hubNotificationsProvider->getNotifications());
    }

    #[Test]
    public function it_returns_a_notification_if_the_current_version_is_different_than_latest(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $stream = $this->createMock(StreamInterface::class);
        $externalResponse = $this->createMock(ResponseInterface::class);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with('latest_sylius_version', $this->isType('callable'))
            ->willReturnCallback(fn ($key, $callback) => $callback());

        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(new Request());

        $this->clock
            ->expects($this->once())
            ->method('now')
            ->willReturn(new \DateTimeImmutable());

        $this->streamFactory
            ->expects($this->once())
            ->method('createStream')
            ->willReturn($stream);

        $content = json_encode(['version' => '1.0.0']);
        $stream
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($content);

        $this->requestFactory
            ->expects($this->once())
            ->method('createRequest')
            ->willReturn($request);

        $request
            ->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturn($request);

        $request
            ->expects($this->once())
            ->method('withBody')
            ->with($stream)
            ->willReturn($request);

        $externalResponse
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $this->client
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn($externalResponse);

        $notifications = $this->hubNotificationsProvider->getNotifications();

        $this->assertNotEmpty($notifications);
        $this->assertArrayHasKey('latest_sylius_version', $notifications);
        $this->assertSame($notifications['latest_sylius_version'], [
            'message' => 'sylius.ui.notifications.new_version_of_sylius_available',
            'latest_version' => '1.0.0',
        ]);
    }
}
