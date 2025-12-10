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

final class HubNotificationProviderTest extends TestCase
{
    public function testDoesNotReturnNotificationIfClientExceptionOccurs(): void
    {
        $provider = $this->createProvider(
            hubResponse: $this->createMock(ClientExceptionInterface::class),
        );

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
        $provider = $this->createProvider(hubResponse: ['version' => '1.0.0']);

        $this->assertSame([
            'latest_sylius_version' => [
                'message' => 'sylius.ui.notifications.new_version_of_sylius_available',
                'latest_version' => '1.0.0',
            ],
        ], $provider->getNotifications());
    }

    public function testDoesNotReturnNotificationIfHubResponseHasNoVersion(): void
    {
        $provider = $this->createProvider(hubResponse: ['foo' => 'bar']);

        $this->assertEmpty($provider->getNotifications());
    }

    private function createProvider(array|\Throwable $hubResponse): HubNotificationProvider
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('withHeader')->willReturnSelf();
        $request->method('withBody')->willReturnSelf();

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->method('createRequest')->willReturn($request);

        $stream = $this->createMock(StreamInterface::class);

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->willReturn($stream);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn(new Request());

        $clock = $this->createMock(ClockInterface::class);
        $clock->method('now')->willReturn(new \DateTimeImmutable());

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')->willReturnCallback(fn ($key, $callback) => $callback());

        $client = $this->createMock(ClientInterface::class);

        if ($hubResponse instanceof \Throwable) {
            $client->method('sendRequest')->willThrowException($hubResponse);
        } else {
            $stream->method('getContents')->willReturn(json_encode($hubResponse));
            $response = $this->createMock(ResponseInterface::class);
            $response->method('getBody')->willReturn($stream);
            $client->method('sendRequest')->willReturn($response);
        }

        return new HubNotificationProvider(
            $client,
            $requestStack,
            $requestFactory,
            $streamFactory,
            $cache,
            $clock,
            'https://hub.example.com',
            'prod',
            true,
            60,
        );
    }
}
