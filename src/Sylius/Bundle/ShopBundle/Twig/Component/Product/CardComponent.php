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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
class CardComponent
{
    use HookableComponentTrait;

    #[ExposeInTemplate(name: 'product')]
    public ?ProductInterface $product = null;

    public ?ProductVariantInterface $variant = null;

    public ?string $slug = null;

    /**
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     */
    public function __construct(
        protected readonly ProductRepositoryInterface $productRepository,
        protected readonly ProductVariantResolverInterface $productVariantResolver,
        protected readonly ChannelContextInterface $channelContext,
        protected readonly LocaleContextInterface $localeContext,
        protected readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        if ($this->product === null && $this->slug !== null) {
            $this->setProductBySlug($this->slug);
        }
    }

    #[ExposeInTemplate(name: 'variant')]
    public function getProductVariant(): ?ProductVariantInterface
    {
        $variant = $this->productVariantResolver->getVariant($this->product);

        if (!$variant instanceof ProductVariantInterface) {
            $variant = $this->product->getVariants()->first();
        }

        if (!$variant instanceof ProductVariantInterface) {
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
        $channel = $this->channelContext->getChannel();

        $originalPrice = $this->productVariantPricesCalculator
            ->calculateOriginal($this->variant, ['channel' => $channel]);

        $price = $this->productVariantPricesCalculator
            ->calculate($this->variant, ['channel' => $channel]);

        return $originalPrice > $price;
    }

    private function setProductBySlug(string $slug): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $this->product = $this->productRepository->findOneByChannelAndSlug($channel, $this->localeContext->getLocaleCode(), $slug);
    }
}
