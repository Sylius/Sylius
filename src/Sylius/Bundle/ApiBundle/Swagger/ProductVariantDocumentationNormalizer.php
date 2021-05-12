<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Swagger;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/** @experimental */
final class ProductVariantDocumentationNormalizer implements NormalizerInterface
{
    /** @var NormalizerInterface */
    private $decoratedNormalizer;

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

        $docs['components']['schemas']['ProductVariant:jsonld-product_variant:read']['properties']['price'] = [
            'type' => 'int',
            'readOnly' => true,
            'default' => 0
        ];

        return $docs;
    }
}
