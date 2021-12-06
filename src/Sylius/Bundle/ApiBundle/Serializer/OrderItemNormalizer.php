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

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

/** @experimental */
final class OrderItemNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_order_item_normalizer_already_called';

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var SectionProviderInterface */
    private $uriBasedSectionContext;

    public function __construct(ChannelContextInterface $channelContext, SectionProviderInterface $uriBasedSectionContext)
    {
        $this->channelContext = $channelContext;
        $this->uriBasedSectionContext = $uriBasedSectionContext;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, OrderItemInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        /** @var ProductVariantInterface $variant */
        $variant = $object->getVariant();

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $variant->getChannelPricingForChannel($channel);

        $data['originalPrice'] = $channelPricing->getOriginalPrice();

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof OrderItemInterface && $this->isNotAdminApiSection();
    }

    private function isNotAdminApiSection(): bool
    {
        return !$this->uriBasedSectionContext->getSection() instanceof AdminApiSection;
    }
}
