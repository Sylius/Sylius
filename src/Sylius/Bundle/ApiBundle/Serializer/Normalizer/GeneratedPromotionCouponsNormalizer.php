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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\Serializer\SerializationGroupsSupportTrait;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class GeneratedPromotionCouponsNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    use SerializationGroupsSupportTrait;

    private const ALREADY_CALLED = 'sylius_generated_promotion_coupons_normalizer_already_called';

    public function __construct(
        private readonly SectionProviderInterface $sectionProvider,
        private readonly array $serializationGroups,
    ) {
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, ArrayCollection::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);
        Assert::isInstanceOf($this->sectionProvider->getSection(), AdminApiSection::class);
        Assert::true($this->supportsSerializationGroups($context, $this->serializationGroups));

        $context[self::ALREADY_CALLED] = true;

        foreach ($object as $promotionCoupon) {
            if ($promotionCoupon instanceof PromotionCouponInterface) {
                $data[] = $this->normalizer->normalize($promotionCoupon, $format, $context);
            }
        }

        return $data ?? $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return
            $data instanceof ArrayCollection &&
            $this->sectionProvider->getSection() instanceof AdminApiSection &&
            $this->supportsSerializationGroups($context, $this->serializationGroups)
        ;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Collection::class => false];
    }
}
