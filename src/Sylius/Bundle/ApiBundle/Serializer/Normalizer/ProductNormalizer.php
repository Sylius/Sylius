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

namespace Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use ApiPlatform\Metadata\IriConverterInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\SerializationGroupsSupportTrait;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ProductNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    use SerializationGroupsSupportTrait;

    private const ALREADY_CALLED = 'sylius_product_normalizer_already_called';

    public function __construct(
        private readonly ProductVariantResolverInterface $defaultProductVariantResolver,
        private readonly IriConverterInterface $iriConverter,
        private readonly SectionProviderInterface $sectionProvider,
        private readonly array $serializationGroups,
    ) {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, ProductInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);
        Assert::true($this->supportsSerializationGroups($context, $this->serializationGroups));

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        $data['variants'] = $object
            ->getEnabledVariants()
            ->map(fn (ProductVariantInterface $variant): string => $this->iriConverter->getIriFromResource($variant))
            ->getValues()
        ;

        $defaultVariant = $this->defaultProductVariantResolver->getVariant($object);

        $data['defaultVariant'] = $defaultVariant === null ? null : $this->iriConverter->getIriFromResource($defaultVariant);

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return
            !isset($context[self::ALREADY_CALLED]) &&
            $data instanceof ProductInterface &&
            $this->sectionProvider->getSection() instanceof ShopApiSection &&
            $this->supportsSerializationGroups($context, $this->serializationGroups)
        ;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [ProductInterface::class => false];
    }
}
