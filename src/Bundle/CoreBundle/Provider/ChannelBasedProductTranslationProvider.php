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

namespace Sylius\Bundle\CoreBundle\Provider;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class ChannelBasedProductTranslationProvider implements ChannelBasedProductTranslationProviderInterface
{
    public function __construct(private LocaleContextInterface $localeContext)
    {
    }

    public function provide(ProductInterface $product, ChannelInterface $channel): ?ProductTranslationInterface
    {
        /** @var Collection<array-key, ProductTranslationInterface> $productTranslations */
        $productTranslations = $product->getTranslations();

        $contextLocaleCode = $this->localeContext->getLocaleCode();
        $productTranslation = $this->findTranslationWithSlugForLocales($productTranslations, [$contextLocaleCode]);

        if (null !== $productTranslation) {
            return $productTranslation;
        }

        /** @var string $channelDefaultLocaleCode */
        $channelDefaultLocaleCode = $channel->getDefaultLocale()->getCode();
        $productTranslation = $this->findTranslationWithSlugForLocales($productTranslations, [$channelDefaultLocaleCode]);

        if (null !== $productTranslation) {
            return $productTranslation;
        }

        $localesEnabledInChannel = $this->getLocalesCodesEnabledInChannel($channel);
        $productTranslation = $this->findTranslationWithSlugForLocales($productTranslations, $localesEnabledInChannel);

        return $productTranslation;
    }

    /**
     * @param Collection<array-key, ProductTranslationInterface> $productTranslations
     * @param array<string> $localeCodes
     */
    private function findTranslationWithSlugForLocales(Collection $productTranslations, array $localeCodes): ?ProductTranslationInterface
    {
        foreach ($productTranslations as $productTranslation) {
            $isLocaleCodeMatching = in_array($productTranslation->getLocale(), $localeCodes);
            $isSlugPresent = '' !== $productTranslation->getSlug() && null !== $productTranslation->getSlug();

            if ($isLocaleCodeMatching && $isSlugPresent) {
                return $productTranslation;
            }
        }

        return null;
    }

    /** @return array<array-key, string> */
    private function getLocalesCodesEnabledInChannel(ChannelInterface $channel): array
    {
        return $channel->getLocales()->map(function (LocaleInterface $locale): string {
            return $locale->getCode();
        })->toArray();
    }
}
