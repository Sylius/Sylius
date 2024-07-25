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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Common;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class ProductCardComponent
{
    public ProductInterface $product;

    public ProductVariantInterface $variant;

    public function __construct(
        private readonly ProductVariantResolverInterface $productVariantResolver,
        private readonly ChannelContextInterface $channelContext,
        private readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ) {
    }

    #[ExposeInTemplate(name: 'variant')]
    public function getProductVariant(): ?ProductVariantInterface
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $this->productVariantResolver->getVariant($this->product);

        if (null === $variant) {
            throw new \InvalidArgumentException('Product has no variants');
        }

        $this->variant = $variant;

        return $this->variant;
    }

    /** @return Collection<array-key, ChannelPricingInterface> */
    #[ExposeInTemplate(name: 'applied_promotions')]
    public function getAppliedPromotions(): Collection
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        return $this->variant->getAppliedPromotionsForChannel($channel);
    }

    #[ExposeInTemplate(name: 'has_discount')]
    public function hasDiscount(): bool
    {
        return
            $this->productVariantPricesCalculator
                ->calculateOriginal($this->variant, ['channel' => $this->channelContext->getChannel()])
            >
            $this->productVariantPricesCalculator
                ->calculate($this->variant, ['channel' => $this->channelContext->getChannel()]);
    }
}
