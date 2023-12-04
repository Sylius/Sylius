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

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class NotificationController
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private string $hubUri,
        private string $environment,
        private StreamFactoryInterface $streamFactory,
    ) {
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
            ->withBody($this->streamFactory->createStream($content))
        ;

        try {
            $hubResponse = $this->client->sendRequest($hubRequest);
        } catch (ClientExceptionInterface) {
            return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
        }

        $hubResponse = json_decode($hubResponse->getBody()->getContents(), true);

        return new JsonResponse($hubResponse);
    }
}
