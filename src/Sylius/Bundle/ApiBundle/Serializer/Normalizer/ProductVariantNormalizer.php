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
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\ApiBundle\Serializer\SerializationGroupsSupportTrait;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ProductVariantNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    use SerializationGroupsSupportTrait;

    private const ALREADY_CALLED = 'sylius_product_variant_normalizer_already_called';

    public function __construct(
        private readonly ProductVariantPricesCalculatorInterface $priceCalculator,
        private readonly AvailabilityCheckerInterface $availabilityChecker,
        private readonly SectionProviderInterface $uriBasedSectionContext,
        private readonly IriConverterInterface $iriConverter,
        private readonly array $serializationGroups,
    ) {
    }

    /**
     * @param ProductVariantInterface $object
     *
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, ProductVariantInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);
        Assert::isInstanceOf($this->uriBasedSectionContext->getSection(), ShopApiSection::class);
        Assert::true($this->supportsSerializationGroups($context, $this->serializationGroups));

        $context[self::ALREADY_CALLED] = true;
        $data = $this->normalizer->normalize($object, $format, $context);

        $data['inStock'] = $this->availabilityChecker->isStockAvailable($object);

        $channel = $context[ContextKeys::CHANNEL] ?? null;
        if (!$channel instanceof ChannelInterface) {
            return $data;
        }
        Assert::isInstanceOf($channel, ChannelInterface::class);

        try {
            $data['price'] = $this->priceCalculator->calculate($object, ['channel' => $channel]);
            $data['originalPrice'] = $this->priceCalculator->calculateOriginal($object, ['channel' => $channel]);
            $data['lowestPriceBeforeDiscount'] = $this->priceCalculator->calculateLowestPriceBeforeDiscount(
                $object,
                ['channel' => $channel],
            );
        } catch (MissingChannelConfigurationException) {
            unset($data['price'], $data['originalPrice'], $data['lowestPriceBeforeDiscount']);
        }

        /** @var Collection<array-key, CatalogPromotionInterface> $appliedPromotions */
        $appliedPromotions = $object->getAppliedPromotionsForChannel($channel);
        if (!$appliedPromotions->isEmpty()) {
            $data['appliedPromotions'] = $appliedPromotions
                ->map(fn (CatalogPromotionInterface $catalogPromotion) => $this->iriConverter->getIriFromResource(
                    resource: $catalogPromotion,
                    context: $context,
                ))
                ->toArray()
            ;
        }

        return $data;
    }

    /** @param array<string, mixed> $context */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return
            $data instanceof ProductVariantInterface &&
            $this->uriBasedSectionContext->getSection() instanceof ShopApiSection &&
            $this->supportsSerializationGroups($context, $this->serializationGroups)
        ;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [ProductVariantInterface::class => false];
    }
}
