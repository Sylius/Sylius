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
final class ProductDocumentationNormalizer implements NormalizerInterface
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

        $defaultVariantSchema = [
            'type' => 'string',
            'format' => 'iri-reference',
            'nullable' => true,
            'readOnly' => true,
        ];

        $docs['components']['schemas']['Product.jsonld-admin.product.read']['properties']['defaultVariant'] = $defaultVariantSchema;
        $docs['components']['schemas']['Product.jsonld-shop.product.read']['properties']['defaultVariant'] = $defaultVariantSchema;

        return $docs;
    }
}
