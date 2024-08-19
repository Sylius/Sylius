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

use Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelperInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class ProductShowPriceComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp]
    public ProductVariant $productVariant;

    public function __construct(
        private readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        private readonly FormatMoneyHelperInterface $formatMoneyHelper,
        private readonly ChannelContextInterface $channelContext,
        private readonly LocaleContextInterface $localeContext,
        private readonly CurrencyContextInterface $currencyContext,
    ) {
    }

    #[LiveListener('variantChanged')]
    public function updateProductVariant(
        #[LiveArg] string $productVariantCode,
    ): void {
        $this->productVariant = $this->resolveProductVariant($productVariantCode);
    }

    #[ExposeInTemplate(name: 'has_discount')]
    public function hasDiscount(): bool
    {
        $channel = $this->channelContext->getChannel();

        return
            $this->productVariantPricesCalculator
                ->calculateOriginal($this->productVariant, ['channel' => $channel])
            >
            $this->productVariantPricesCalculator
                ->calculate($this->productVariant, ['channel' => $channel])
        ;
    }

    #[ExposeInTemplate(name: 'price')]
    public function getPrice(): string
    {
        $price = $this->productVariantPricesCalculator
            ->calculate($this->productVariant, ['channel' => $this->channelContext->getChannel()]);

        return $this->formatPrice($price);
    }

    #[ExposeInTemplate(name: 'original_price')]
    public function getOriginalPrice(): string
    {
        $price = $this->productVariantPricesCalculator
            ->calculateOriginal($this->productVariant, ['channel' => $this->channelContext->getChannel()]);

        return $this->formatPrice($price);
    }

    private function formatPrice(int $price): string
    {
        return $this->formatMoneyHelper->formatAmount(
            $price,
            $this->currencyContext->getCurrencyCode(),
            $this->localeContext->getLocaleCode(),
        );
    }

    private function resolveProductVariant(string $productVariantCode): ProductVariant
    {
        /** @var ProductInterface $product */
        $product = $this->productVariant->getProduct();

        $variants = $product->getEnabledVariants();

        if ($product->getVariantSelectionMethod() === ProductInterface::VARIANT_SELECTION_MATCH) {
            /** @var ProductVariant $variant */
            foreach ($variants as $variant) {
                $values = $variant->getOptionValues();

                foreach ($values as $value) {
                    if ($value->getCode() === $productVariantCode) {
                        return $variant;
                    }
                }

            }
        } else {
            /** @var ProductVariant $variant */
            foreach ($variants as $variant) {
                if ($variant->getCode() === $productVariantCode) {
                    return $variant;
                }
            }
        }

        throw new \InvalidArgumentException('Product variant with code "' . $productVariantCode . '" not found.');
    }
}
