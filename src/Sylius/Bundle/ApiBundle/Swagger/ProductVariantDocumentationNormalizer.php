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
final class ProductVariantDocumentationNormalizer implements NormalizerInterface
{
    public function __construct(private NormalizerInterface $decoratedNormalizer)
    {
    }

    public function supportsNormalization($data, $format = null)
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decoratedNormalizer->normalize($object, $format, $context);

        $docs['components']['schemas']['ProductVariant.jsonld-shop.product_variant.read']['properties']['price'] = [
            'type' => 'integer',
            'readOnly' => true,
            'default' => 0,
        ];

        $docs['components']['schemas']['ProductVariant.jsonld-shop.product_variant.read']['properties']['inStock'] = [
            'type' => 'boolean',
            'readOnly' => true,
        ];

        $docs['components']['schemas']['ProductVariant.jsonld-shop.product_variant.read']['properties']['originalPrice'] = [
            'type' => 'integer',
            'readOnly' => true,
            'default' => 0,
        ];

        return $docs;
    }
}
