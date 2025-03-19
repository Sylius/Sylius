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

namespace Sylius\Bundle\AdminBundle\Tests\Notification;

use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
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
    use ProphecyTrait;

    private ClientInterface|ObjectProphecy $client;

    private ObjectProphecy|RequestStack $requestStack;

    private ObjectProphecy|RequestFactoryInterface $requestFactory;

    private ObjectProphecy|StreamFactoryInterface $streamFactory;

    private CacheInterface|ObjectProphecy $cache;

    private ClockInterface|ObjectProphecy $clock;

    private HubNotificationProvider $hubNotificationsProvider;

    private static string $hubUri = 'www.doesnotexist.test.com';

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->prophesize(ClientInterface::class);
        $this->requestStack = $this->prophesize(RequestStack::class);
        $this->requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $this->streamFactory = $this->prophesize(StreamFactoryInterface::class);
        $this->cache = $this->prophesize(CacheInterface::class);
        $this->clock = $this->prophesize(ClockInterface::class);

        $this->hubNotificationsProvider = new HubNotificationProvider(
            $this->client->reveal(),
            $this->requestStack->reveal(),
            $this->requestFactory->reveal(),
            $this->streamFactory->reveal(),
            $this->cache->reveal(),
            $this->clock->reveal(),
            self::$hubUri,
            'prod',
            true,
            60,
        );
    }

    /** @test */
    public function it_returns_an_empty_array_if_client_exception_occurs(): void
    {
        $request = $this->prophesize(RequestInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $this->cache->get('latest_sylius_version', Argument::type('callable'))->will(function ($args) {
            return $args[1]();
        });

        $this->requestStack->getCurrentRequest()->willReturn(new Request());
        $this->clock->now()->willReturn(new \DateTimeImmutable());
        $this->streamFactory->createStream(Argument::cetera())->willReturn($stream);

        $this->requestFactory->createRequest(Argument::cetera())->willReturn($request);
        $request->withHeader('Content-Type', 'application/json')->willReturn($request);
        $request->withBody($stream)->willReturn($request);

        $this->client->sendRequest(Argument::cetera())->willThrow(ConnectException::class);

        $this->assertEmpty($this->hubNotificationsProvider->getNotifications());
    }

    /** @test */
    public function it_returns_an_empty_array_if_the_current_version_is_the_same_as_latest(): void
    {
        $request = $this->prophesize(RequestInterface::class);
        $stream = $this->prophesize(StreamInterface::class);
        $externalResponse = $this->prophesize(ResponseInterface::class);

        $this->cache->get('latest_sylius_version', Argument::type('callable'))->will(function ($args) {
            return $args[1]();
        });

        $this->requestStack->getCurrentRequest()->willReturn(new Request());
        $this->clock->now()->willReturn(new \DateTimeImmutable());
        $this->streamFactory->createStream(Argument::cetera())->willReturn($stream);

        $content = json_encode(['version' => SyliusCoreBundle::VERSION]);
        $stream->getContents()->willReturn($content);

        $this->requestFactory->createRequest(Argument::cetera())->willReturn($request);
        $request->withHeader('Content-Type', 'application/json')->willReturn($request);
        $request->withBody($stream)->willReturn($request);

        $externalResponse->getBody()->willReturn($stream->reveal());
        $this->client->sendRequest(Argument::cetera())->willReturn($externalResponse->reveal());

        $this->assertEmpty($this->hubNotificationsProvider->getNotifications());
    }

    /** @test */
    public function it_returns_a_notification_if_the_current_version_is_different_than_latest(): void
    {
        $request = $this->prophesize(RequestInterface::class);
        $stream = $this->prophesize(StreamInterface::class);
        $externalResponse = $this->prophesize(ResponseInterface::class);

        $this->cache->get('latest_sylius_version', Argument::type('callable'))->will(function ($args) {
            return $args[1]();
        });

        $this->requestStack->getCurrentRequest()->willReturn(new Request());
        $this->clock->now()->willReturn(new \DateTimeImmutable());
        $this->streamFactory->createStream(Argument::cetera())->willReturn($stream);

        $content = json_encode(['version' => '1.0.0']);
        $stream->getContents()->willReturn($content);

        $this->requestFactory->createRequest(Argument::cetera())->willReturn($request);
        $request->withHeader('Content-Type', 'application/json')->willReturn($request);
        $request->withBody($stream)->willReturn($request);

        $externalResponse->getBody()->willReturn($stream->reveal());
        $this->client->sendRequest(Argument::cetera())->willReturn($externalResponse->reveal());

        $notifications = $this->hubNotificationsProvider->getNotifications();

        $this->assertNotEmpty($notifications);
        $this->assertArrayHasKey('latest_sylius_version', $notifications);
        $this->assertSame($notifications['latest_sylius_version'], [
            'message' => 'sylius.ui.notifications.new_version_of_sylius_available',
            'latest_version' => '1.0.0',
        ]);
    }
}
