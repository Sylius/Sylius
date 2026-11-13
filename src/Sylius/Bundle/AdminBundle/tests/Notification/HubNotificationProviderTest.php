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

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientExceptionInterface;
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
use Symfony\Contracts\Cache\ItemInterface;

#[AllowMockObjectsWithoutExpectations]
final class HubNotificationProviderTest extends TestCase
{
    public function testDoesNotReturnNotificationIfClientExceptionOccurs(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client
            ->method('sendRequest')
            ->willThrowException($this->createMock(ClientExceptionInterface::class));

        $provider = $this->createProvider(
            hubResponse: [],
            client: $client,
        );

        $this->assertEmpty($provider->getNotifications());
    }

    public function testItDoesNotReturnNotificationIfThereIsNoCurrentRequest(): void
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn(null);

        $provider = $this->createProvider(requestStack: $requestStack);

        $this->assertEmpty($provider->getNotifications());
    }

    public function testDoesNotReturnNotificationIfCurrentVersionIsSameAsLatest(): void
    {
        $provider = $this->createProvider(
            hubResponse: ['version' => SyliusCoreBundle::VERSION],
        );

        $this->assertEmpty($provider->getNotifications());
    }

    public function testReturnsNotificationIfVersionIsDifferent(): void
    {
        $provider = $this->createProvider(hubResponse: ['version' => 'NEW-VERSION']);

        $this->assertSame([
            'latest_sylius_version' => [
                'message' => 'sylius.ui.notifications.new_version_of_sylius_available',
                'latest_version' => 'NEW-VERSION',
            ],
        ], $provider->getNotifications());
    }

    public function testDoesNotReturnNotificationIfHubResponseHasNoVersion(): void
    {
        $provider = $this->createProvider(hubResponse: ['foo' => 'bar']);

        $this->assertEmpty($provider->getNotifications());
    }

    public function testDoesNotCallHubWhenCacheExists(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->method('get')
            ->with(HubNotificationProvider::LATEST_SYLIUS_VERSION_KEY)
            ->willReturn('2.1.7');

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->never())->method('sendRequest');

        $provider = $this->createProvider(
            hubResponse: ['version' => '2.1.7'],
            cache: $cache,
            client: $client,
        );

        $this->assertSame([
            'latest_sylius_version' => [
                'message' => 'sylius.ui.notifications.new_version_of_sylius_available',
                'latest_version' => '2.1.7',
            ],
        ], $provider->getNotifications());
    }

    public function testCallsHubWhenCacheExpired(): void
    {
        $checkFrequencyInMinutes = 60;

        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem
            ->expects($this->once())
            ->method('expiresAfter')
            ->with($checkFrequencyInMinutes * 60);

        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->method('get')
            ->with(HubNotificationProvider::LATEST_SYLIUS_VERSION_KEY, $this->anything())
            ->willReturnCallback(
                function (string $key, callable $callback) use ($cacheItem): ?string {
                    return $callback($cacheItem);
                },
            );

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn(json_encode(['version' => '2.1.7']));

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())->method('sendRequest')->willReturn($response);

        $provider = $this->createProvider(
            hubResponse: ['version' => '2.1.7'],
            cache: $cache,
            client: $client,
            checkFrequency: $checkFrequencyInMinutes,
        );

        $this->assertSame([
            'latest_sylius_version' => [
                'message' => 'sylius.ui.notifications.new_version_of_sylius_available',
                'latest_version' => '2.1.7',
            ],
        ], $provider->getNotifications());
    }

    public function testSupportsReturnsTrueWhenHubNotificationsEnabled(): void
    {
        $provider = $this->createProvider(areHubNotificationsEnabled: true);

        $this->assertTrue($provider->supports());
    }

    public function testSupportsReturnsFalseWhenHubNotificationsDisabled(): void
    {
        $provider = $this->createProvider(areHubNotificationsEnabled: false);

        $this->assertFalse($provider->supports());
    }

    private function createProvider(
        array $hubResponse = [],
        ?CacheInterface $cache = null,
        ?ClientInterface $client = null,
        int $checkFrequency = 60,
        bool $areHubNotificationsEnabled = true,
        ?RequestStack $requestStack = null,
    ): HubNotificationProvider {
        $request = $this->createMock(RequestInterface::class);
        $request->method('withHeader')->willReturnSelf();
        $request->method('withBody')->willReturnSelf();

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->method('createRequest')->willReturn($request);

        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn(json_encode($hubResponse));

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->willReturn($stream);

        if ($requestStack === null) {
            $requestStack = $this->createMock(RequestStack::class);
            $requestStack->method('getCurrentRequest')->willReturn(new Request());
        }

        if ($cache === null) {
            $cacheItem = $this->createMock(ItemInterface::class);
            $cache = $this->createMock(CacheInterface::class);
            $cache->method('get')->willReturnCallback(fn ($key, $callback) => $callback($cacheItem));
        }

        if ($client === null) {
            $response = $this->createMock(ResponseInterface::class);
            $response->method('getBody')->willReturn($stream);

            $client = $this->createMock(ClientInterface::class);
            $client->method('sendRequest')->willReturn($response);
        }

        return new HubNotificationProvider(
            $client,
            $requestStack,
            $requestFactory,
            $streamFactory,
            $cache,
            $this->createMock(ClockInterface::class),
            'https://hub.example.com',
            'prod',
            $areHubNotificationsEnabled,
            $checkFrequency,
        );
    }
}
