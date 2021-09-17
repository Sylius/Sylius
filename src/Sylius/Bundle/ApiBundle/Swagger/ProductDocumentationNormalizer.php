<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/** @experimental */
final class ProductDocumentationNormalizer implements NormalizerInterface
{
    private NormalizerInterface $decoratedNormalizer;

    public function __construct(NormalizerInterface $decoratedNormalizer)
    {
        $this->decoratedNormalizer = $decoratedNormalizer;
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
