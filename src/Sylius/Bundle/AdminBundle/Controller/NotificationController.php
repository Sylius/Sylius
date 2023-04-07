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

namespace Sylius\Bundle\AdminBundle\Controller;

use GuzzleHttp\ClientInterface as DeprecatedClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Http\Message\RequestFactory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NotificationController
{
    public function __construct(
        private ClientInterface|DeprecatedClientInterface $client,
        private RequestFactory $requestFactory,
        private string $hubUri,
        private string $environment,
    ) {
        if ($client instanceof DeprecatedClientInterface) {
            trigger_deprecation('sylius/admin-bundle', '1.13', 'Using "%s" is deprecated and will be prohibited in 2.0, use "%s" instead.', DeprecatedClientInterface::class, ClientInterface::class);
        }
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

        $hubRequest = $this->requestFactory->createRequest(
            Request::METHOD_GET,
            $this->hubUri,
            $headers,
            json_encode($content),
        );

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
