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

namespace Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Symfony\Component\HttpFoundation\Request;

final class HttpRequestMethodTypeContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(private SerializerContextBuilderInterface $decoratedLocaleBuilder)
    {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decoratedLocaleBuilder->createFromRequest($request, $normalization, $extractedAttributes);

        $context[ContextKeys::HTTP_REQUEST_METHOD_TYPE] = strtoupper($request->getMethod());

        return $context;
    }
}
