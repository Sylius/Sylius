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

namespace Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

/** @experimental */
final class ProductVariantNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_product_variant_normalizer_already_called';

    public function __construct(
        private ProductVariantPricesCalculatorInterface $priceCalculator,
        private AvailabilityCheckerInterface $availabilityChecker,
        private SectionProviderInterface $uriBasedSectionContext,
        private IriConverterInterface $iriConverter,
    ) {
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ProductVariantInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

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
            unset($data['price'], $data['originalPrice']);
        }

        /** @var ArrayCollection $appliedPromotions */
        $appliedPromotions = $object->getAppliedPromotionsForChannel($channel);
        if (!$appliedPromotions->isEmpty()) {
            $data['appliedPromotions'] = array_map(
                fn (CatalogPromotionInterface $catalogPromotion) => $this->iriConverter->getIriFromResource($catalogPromotion),
                $appliedPromotions->toArray(),
            );
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof ProductVariantInterface && $this->isNotAdminApiSection();
    }

    private function isNotAdminApiSection(): bool
    {
        return !$this->uriBasedSectionContext->getSection() instanceof AdminApiSection;
    }
}
