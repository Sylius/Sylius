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
class IriReferenceExampleNormalizer implements NormalizerInterface
{
    public function __construct(
        private NormalizerInterface $decoratedNormalizer
    ) { }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decoratedNormalizer->normalize($object, $format, $context);

        foreach ($docs['components']['schemas'] as $schema) {
            if (!isset($schema['properties'])) {
                continue;
            }

            foreach ($schema['properties'] as $property) {
                if (isset($property['type']) && isset($property['format']) && $property['format'] === 'iri-reference' && !isset($property['example'])) {
                    $property['example'] = 'iri-reference';
                }

                if (isset($property['type']) && $property['type'] === 'array' && isset($property['items']['format']) && !isset($property['items']['example'])) {
                    $property['items']['example'] = 'iri-reference';
                }
            }
        }

        return $docs;
    }
}
