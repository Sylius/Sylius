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

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
class PriceComponent
{
    use HookableComponentTrait;

    public ProductVariantInterface $variant;

    #[ExposeInTemplate]
    public ?string $price = null;

    #[ExposeInTemplate(name: 'original_price')]
    public ?string $originalPrice = null;

    #[ExposeInTemplate(name: 'has_discount')]
    public bool $hasDiscount = false;

    public function __construct(
        protected readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        protected readonly MoneyFormatterInterface $moneyFormatter,
        protected readonly ChannelContextInterface $channelContext,
        protected readonly LocaleContextInterface $localeContext,
        protected readonly CurrencyContextInterface $currencyContext,
        protected readonly CurrencyConverterInterface $currencyConverter,
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        $price = $this->convertPrice(
            $this->productVariantPricesCalculator
                ->calculate($this->variant, ['channel' => $this->channelContext->getChannel()]),
        );

        $originalPrice = $this->convertPrice(
            $this->productVariantPricesCalculator
                ->calculateOriginal($this->variant, ['channel' => $this->channelContext->getChannel()]),
        );

        $this->price = $this->formatPrice($price);
        $this->originalPrice = $this->formatPrice($originalPrice);
        $this->hasDiscount = $originalPrice > $price;
    }

    private function convertPrice(int $price): int
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        return $this->currencyConverter->convert(
            $price,
            $channel->getBaseCurrency()->getCode(),
            $this->currencyContext->getCurrencyCode(),
        );
    }

    private function formatPrice(int $price): string
    {
        return $this->moneyFormatter->format(
            $price,
            $this->currencyContext->getCurrencyCode(),
            $this->localeContext->getLocaleCode(),
        );
    }
}
