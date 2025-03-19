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

namespace Sylius\Bundle\PaymentBundle\Normalizer;

use Symfony\Component\HttpFoundation\Request;

/** @experimental */
final class SymfonyRequestNormalizer implements SymfonyRequestNormalizerInterface
{
    public function normalize(Request $request): array
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
            ],
        ];
    }
}
