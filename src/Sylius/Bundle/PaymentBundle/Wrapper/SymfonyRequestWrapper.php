<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Wrapper;

use Symfony\Component\HttpFoundation\Request;

final class SymfonyRequestWrapper implements SymfonyRequestWrapperInterface
{
    public function wrap(Request $request): array
    {
        return [
            'http_request' => [
                'uri' => $request->getUri(),
                'method' => $request->getMethod(),
                'query' => $request->query->all(),
                'request' => $request->request->all(),
                'headers' => $request->headers->all(),
                'content' => $request->getContent(),
                'clientIp' => $request->getClientIp(),
            ]
        ];
    }
}
