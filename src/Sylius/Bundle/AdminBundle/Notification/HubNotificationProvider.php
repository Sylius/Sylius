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

namespace Sylius\Bundle\AdminBundle\Notification;

use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class HubNotificationProvider implements NotificationProviderInterface
{
    public const LATEST_SYLIUS_VERSION_KEY = 'latest_sylius_version';

    public function __construct(
        private ClientInterface $client,
        private RequestStack $requestStack,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private CacheInterface $cache,
        private ClockInterface $clock,
        private string $hubUri,
        private string $environment,
        private bool $areHubNotificationsEnabled,
        private int $checkFrequency,
    ) {
    }

    public function getNotifications(array $context = []): array
    {
        $metadata = $this->cache instanceof ItemInterface ? $this->cache->getMetadata() : [];
        $metadata[ItemInterface::METADATA_EXPIRY] = $this->clock->now()->modify(sprintf('+%d minutes', $this->checkFrequency))->getTimestamp();

        $latestVersion = $this->cache->get(self::LATEST_SYLIUS_VERSION_KEY, function (): ?string {
            return $this->getLatestVersion();
        });

        if (
            $latestVersion === null ||
            $latestVersion === SyliusCoreBundle::VERSION
        ) {
            return [];
        }

        return [
            self::LATEST_SYLIUS_VERSION_KEY => [
                'message' => 'sylius.ui.notifications.new_version_of_sylius_available',
                'latest_version' => $latestVersion,
            ],
        ];
    }

    public function supports(array $context = []): bool
    {
        return $this->areHubNotificationsEnabled;
    }

    private function getLatestVersion(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        $content = json_encode([
            'version' => SyliusCoreBundle::VERSION,
            'hostname' => $request->getHost(),
            'locale' => $request->getLocale(),
            'user_agent' => $request->headers->get('User-Agent'),
            'environment' => $this->environment,
        ]);

        $hubRequest = $this->requestFactory
            ->createRequest(Request::METHOD_GET, $this->hubUri)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream($content))
        ;

        try {
            $hubResponse = $this->client->sendRequest($hubRequest);
        } catch (ClientExceptionInterface) {
            return null;
        }

        $responseContent = json_decode($hubResponse->getBody()->getContents(), true);
        if (!isset($responseContent['version'])) {
            return null;
        }

        return strtoupper($responseContent['version']);
    }
}
