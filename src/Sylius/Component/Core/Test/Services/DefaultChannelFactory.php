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

namespace Sylius\Component\Core\Test\Services;

use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class DefaultChannelFactory implements DefaultChannelFactoryInterface
{
    public const DEFAULT_CHANNEL_NAME = 'Default';

    public const DEFAULT_CHANNEL_CODE = 'DEFAULT';

    public const DEFAULT_CHANNEL_CURRENCY = 'USD';

    /** @var ChannelFactoryInterface */
    private $channelFactory;

    /** @var FactoryInterface */
    private $currencyFactory;

    /** @var FactoryInterface */
    private $localeFactory;

    /** @var RepositoryInterface */
    private $channelRepository;

    /** @var RepositoryInterface */
    private $currencyRepository;

    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var string */
    private $defaultLocaleCode;

    public function __construct(
        ChannelFactoryInterface $channelFactory,
        FactoryInterface $currencyFactory,
        FactoryInterface $localeFactory,
        RepositoryInterface $channelRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $localeRepository,
        string $defaultLocaleCode
    ) {
        $this->channelFactory = $channelFactory;
        $this->currencyFactory = $currencyFactory;
        $this->localeFactory = $localeFactory;
        $this->channelRepository = $channelRepository;
        $this->currencyRepository = $currencyRepository;
        $this->localeRepository = $localeRepository;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function create(?string $code = null, ?string $name = null, ?string $currencyCode = null): array
    {
        $currency = $this->provideCurrency($currencyCode);
        $locale = $this->provideLocale();

        /** @var ChannelInterface $channel */
        $channel = $this->channelFactory->createNamed($name ?: self::DEFAULT_CHANNEL_NAME);
        $channel->setCode($code ?: self::DEFAULT_CHANNEL_CODE);
        $channel->setTaxCalculationStrategy('order_items_based');

        $channel->addCurrency($currency);
        $channel->setBaseCurrency($currency);

        $channel->addLocale($locale);
        $channel->setDefaultLocale($locale);

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

    private function provideLocale(): LocaleInterface
    {
        /** @var LocaleInterface|null $locale */
        $locale = $this->localeRepository->findOneBy(['code' => $this->defaultLocaleCode]);

        if (null === $locale) {
            /** @var LocaleInterface $locale */
            $locale = $this->localeFactory->createNew();
            $locale->setCode($this->defaultLocaleCode);

            $this->localeRepository->add($locale);
        }

        return $locale;
    }
}
