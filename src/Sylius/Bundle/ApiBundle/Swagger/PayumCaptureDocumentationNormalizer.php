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

namespace Sylius\Bundle\ApiBundle\Swagger;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/** @experimental */
final class PayumCaptureDocumentationNormalizer implements NormalizerInterface
{
    public function __construct(
        private NormalizerInterface $decoratedNormalizer,
        private string $apiRoute,
    ) {
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decoratedNormalizer->normalize($object, $format, $context);

        $docs['components']['schemas']['PayumCapture'] = [
            'type' => 'object',
            'properties' => [
                'content' => [
                    'type' => 'string',
                    'readOnly' => true,
                    'example' => '
<p>
    <form action="...">
        <p>
            <label>
            Crédit card details:
            <input type="text" name="cc-details">
            </label>
        </p>
        <p>
            <button type="submit">Pay !</button>
        </p>
    </form>
</p>',
                ],
                'statusCode' => [
                    'type' => 'integer',
                    'readOnly' => true,
                    'example' => 200,
                ],
                'headers' => [
                    'type' => 'object',
                    'additionalProperties' => [
                        'type' => 'string',
                    ],
                    'readOnly' => true,
                    'example' => [
                        'Content-Type' => 'text/html',
                        'X-Custom-Header' => 'foo',
                    ],
                ],
            ],
        ];

        $docs['paths'][$this->apiRoute . '/shop/payum/{payum_token}/capture']['get']['responses'][Response::HTTP_OK] = [
            'description' => 'Get Payum capture reply',
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/PayumCapture',
                    ],
                ],
            ]
        ];

        return $docs;
    }
}
