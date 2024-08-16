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

use Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelperInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
final class ProductPriceComponent
{
    public ProductVariantInterface $productVariant;

    public function __construct(
        private readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        private readonly FormatMoneyHelperInterface $formatMoneyHelper,
        private readonly ChannelContextInterface $channelContext,
        private readonly LocaleContextInterface $localeContext,
        private readonly CurrencyContextInterface $currencyContext,
    ) {
    }

    #[ExposeInTemplate(name: 'price')]
    public function getPrice(): string
    {
        $price = $this->productVariantPricesCalculator
            ->calculate($this->productVariant, ['channel' => $this->channelContext->getChannel()]);

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
