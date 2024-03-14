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

namespace Sylius\Bundle\ApiBundle\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/** @experimental */
final class ProductSlugDocumentationNormalizer implements NormalizerInterface
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

        $shopProductBySlugPath = $this->apiRoute . '/shop/products-by-slug/{slug}';

        if (!isset($docs['paths'][$shopProductBySlugPath]['get'])) {
            return $docs;
        }

        $params = $docs['paths'][$shopProductBySlugPath]['get']['parameters'];

        foreach ($params as $index => $param) {
            if ($param['name'] === 'code') {
                unset($docs['paths'][$shopProductBySlugPath]['get']['parameters'][$index]);
            }
        }

        // reset key index after unset
        $docs['paths'][$shopProductBySlugPath]['get']['parameters'] = array_values($docs['paths'][$shopProductBySlugPath]['get']['parameters']);

        return $docs;
    }
}
