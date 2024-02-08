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

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use Http\Message\MessageFactory;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NotificationController
{
    private Uri $hubUri;

    public function __construct(
        private ClientInterface $client,
        private MessageFactory $messageFactory,
        string $hubUri,
        private string $environment,
    ) {
        $this->hubUri = new Uri($hubUri);
    }

    public function getVersionAction(Request $request): JsonResponse
    {
        $content = [
            'version' => SyliusCoreBundle::VERSION,
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
            json_encode($content),
        );

        try {
            $hubResponse = $this->client->send($hubRequest, ['verify' => false]);
        } catch (GuzzleException) {
            return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
        }

        $hubResponse = json_decode($hubResponse->getBody()->getContents(), true);

        return new JsonResponse($hubResponse);
    }
}
