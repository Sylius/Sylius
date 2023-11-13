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

namespace Sylius\Component\Core\Test\Services;

use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class DefaultChannelFactory implements DefaultChannelFactoryInterface
{
    public const DEFAULT_CHANNEL_NAME = 'Default';

    public const DEFAULT_CHANNEL_CODE = 'DEFAULT';

    public const DEFAULT_CHANNEL_CURRENCY = 'USD';

    public function __construct(
        private ChannelFactoryInterface $channelFactory,
        private FactoryInterface $currencyFactory,
        private FactoryInterface $localeFactory,
        private RepositoryInterface $channelRepository,
        private RepositoryInterface $currencyRepository,
        private RepositoryInterface $localeRepository,
        private string $defaultLocaleCode,
    ) {
    }

    public function create(?string $code = null, ?string $name = null, ?string $currencyCode = null, ?string $localeCode = null): array
    {
        $currency = $this->provideCurrency($currencyCode);
        $locale = $this->provideLocale($localeCode);

        /** @var ChannelInterface $channel */
        $channel = $this->channelFactory->createNamed($name ?: self::DEFAULT_CHANNEL_NAME);
        $channel->setCode($code ?: self::DEFAULT_CHANNEL_CODE);
        $channel->setTaxCalculationStrategy('order_items_based');

        $channel->addCurrency($currency);
        $channel->setBaseCurrency($currency);

        $channel->addLocale($locale);
        $channel->setDefaultLocale($locale);
        if ($channel->getShopBillingData() === null) {
            $channel->setShopBillingData(new ShopBillingData());
        }

        $this->channelRepository->add($channel);

        return [
            'channel' => $channel,
            'currency' => $currency,
            'locale' => $locale,
        ];
    }

    private function provideCurrency(?string $currencyCode): CurrencyInterface
    {
        $currencyCode = $currencyCode ?? self::DEFAULT_CHANNEL_CURRENCY;

        /** @var CurrencyInterface|null $currency */
        $currency = $this->currencyRepository->findOneBy(['code' => $currencyCode]);

        if (null === $currency) {
            /** @var CurrencyInterface $currency */
            $currency = $this->currencyFactory->createNew();
            $currency->setCode($currencyCode);

            $this->currencyRepository->add($currency);
        }

        return $currency;
    }

    private function provideLocale(?string $localeCode = null): LocaleInterface
    {
        /** @var LocaleInterface|null $locale */
        $locale = $this->localeRepository->findOneBy(['code' => $this->defaultLocaleCode]);

        if (null === $locale) {
            /** @var LocaleInterface $locale */
            $locale = $this->localeFactory->createNew();
            $locale->setCode($localeCode ?? $this->defaultLocaleCode);

            $this->localeRepository->add($locale);
        }

        return $locale;
    }
}
