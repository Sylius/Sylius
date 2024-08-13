<?php

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product;

use Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelperInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class ProductShowPriceComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    public ProductVariantInterface $productVariant;

    public function __construct(
        private readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        private readonly FormatMoneyHelperInterface $formatMoneyHelper,
        private readonly ChannelContextInterface $channelContext,
        private readonly LocaleContextInterface $localeContext,
        private readonly CurrencyContextInterface $currencyContext,
        private readonly ProductVariantRepositoryInterface $productVariantRepository,
    ){
    }

    #[LiveListener('variantChanged')]
    public function updateProductVariant(#[LiveArg] string $productVariantCode): void
    {
        $selectedProductVariant = $this->productVariantRepository->findOneBy(['code' => $productVariantCode]);

        if ($selectedProductVariant instanceof ProductVariantInterface) {
            $this->productVariant = $selectedProductVariant;
        }
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
}
