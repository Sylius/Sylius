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

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/** @experimental */
final class AcceptLanguageHeaderDocumentationNormalizer implements NormalizerInterface
{
    public function __construct(private NormalizerInterface $decoratedNormalizer)
    {
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decoratedNormalizer->normalize($object, $format, $context);

        $acceptLanguageHeader = [
            'name' => 'Accept-Language',
            'in' => 'header',
            'required' => false,
            'default' => 'en_US',
            'schema' => [
                'type' => 'string'
            ]
        ];

        foreach ($docs['paths'] as $path => $methods) {
            foreach ($methods as $methodName => $methodBody) {
                if (is_object($methodBody)) {
                    $methodBody = $methodBody->getArrayCopy();
                    $methodBody['parameters'][] = $acceptLanguageHeader;

                    $docs['paths'][$path][$methodName] = $methodBody;
                }
            }
        }

        return $docs;
    }
}
