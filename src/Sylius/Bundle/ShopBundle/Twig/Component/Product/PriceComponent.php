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
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class PriceComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp]
    public ProductVariant $variant;

    public function __construct(
        private readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        private readonly FormatMoneyHelperInterface $formatMoneyHelper,
        private readonly ChannelContextInterface $channelContext,
        private readonly LocaleContextInterface $localeContext,
        private readonly CurrencyContextInterface $currencyContext,
    ) {
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

    #[ExposeInTemplate(name: 'price')]
    public function getPrice(): string
    {
        $price = $this->productVariantPricesCalculator
            ->calculate($this->variant, ['channel' => $this->channelContext->getChannel()]);

        return $this->formatPrice($price);
    }

    #[ExposeInTemplate(name: 'original_price')]
    public function getOriginalPrice(): string
    {
        $price = $this->productVariantPricesCalculator
            ->calculateOriginal($this->variant, ['channel' => $this->channelContext->getChannel()]);

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
}
