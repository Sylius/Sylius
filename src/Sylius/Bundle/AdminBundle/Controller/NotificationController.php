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

namespace Sylius\Bundle\AdminBundle\Controller;

use GuzzleHttp\ClientInterface as DeprecatedClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Http\Message\MessageFactory;
use Nyholm\Psr7\Stream;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NotificationController
{
    public function __construct(
        private ClientInterface|DeprecatedClientInterface $client,
        private MessageFactory|RequestFactoryInterface $requestFactory,
        private string $hubUri,
        private string $environment,
        private ?StreamFactoryInterface $streamFactory = null,
    ) {
        if (!$client instanceof ClientInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.13',
                'Using a service that does not implement "%s" as a 1st argument of "%s" constructor is deprecated and will be prohibited in Sylius 2.0.',
                ClientInterface::class,
                self::class,
            );
        }

        if (!$requestFactory instanceof RequestFactoryInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.13',
                'Using a service that does not implement "%s" as a 2nd argument of "%s" constructor is deprecated and will be prohibited in Sylius 2.0.',
                RequestFactoryInterface::class,
                self::class,
            );
        }

        if (null === $streamFactory) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.13',
                'Not passing a service that implements "%s" as a 5th argument of "%s" constructor is deprecated and will be prohibited in Sylius 2.0.',
                StreamFactoryInterface::class,
                self::class,
            );
        }
    }

    public function getVersionAction(Request $request): JsonResponse
    {
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
            ->withBody(
                null === $this->streamFactory
                ? Stream::create($content)
                : $this->streamFactory->createStream($content),
            )
        ;

        try {
            if ($this->client instanceof DeprecatedClientInterface) {
                $hubResponse = $this->client->send($hubRequest, ['verify' => false]);
            } else {
                $hubResponse = $this->client->sendRequest($hubRequest);
            }
        } catch (ClientExceptionInterface|GuzzleException) {
            return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
        }

        $hubResponse = json_decode($hubResponse->getBody()->getContents(), true);

        return new JsonResponse($hubResponse);
    }
}
