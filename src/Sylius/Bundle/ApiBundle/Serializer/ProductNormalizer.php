<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Core\Api\IriConverterInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ProductNormalizer implements ContextAwareNormalizerInterface
{
    /** @var NormalizerInterface */
    private $objectNormalizer;

    /** @var ProductVariantResolverInterface */
    private $defaultProductVariantResolver;

    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(
        NormalizerInterface $objectNormalizer,
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->defaultProductVariantResolver = $defaultProductVariantResolver;
        $this->iriConverter = $iriConverter;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ProductInterface::class);

        $data = $this->objectNormalizer->normalize($object, $format, $context);

        $data['defaultVariant'] = $this->iriConverter->getIriFromItem($this->defaultProductVariantResolver->getVariant($object));

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        return $data instanceof ProductInterface;
    }
}
