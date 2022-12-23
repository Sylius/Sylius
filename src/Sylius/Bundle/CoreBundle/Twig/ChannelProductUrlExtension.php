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

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ChannelProductUrlExtension extends AbstractExtension
{
    public function __construct(
        private ChannelUrlExtension $channelUrlExtension,
        private LocaleContextInterface $localeContext,
        private RouterInterface $router,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_channel_url_for_product', [$this, 'generateChannelUrlForProduct']),
        ];
    }

    public function generateChannelUrlForProduct(ProductInterface $product, ChannelInterface $channel): string
    {
        $defaultChannelLocaleCode = $channel->getDefaultLocale()?->getCode();

        $baseUrl = $this->router->generate('sylius_shop_product_show', [
            'slug' => $this->getSlug($product, $defaultChannelLocaleCode),
            '_locale' => $defaultChannelLocaleCode
        ]);

        return $this->channelUrlExtension->generateChannelUrl($baseUrl, $channel);
    }

    private function getSlug(ProductInterface $product, string $defaultChannelLocaleCode): string
    {
        /** @var array<string, ProductTranslation> $availableTranslations */
        $availableTranslations = $product->getTranslations()->toArray();

        // Try to get the slug from the current admin user locale
        $userLocaleCode = $this->localeContext->getLocaleCode();
        if (array_key_exists($userLocaleCode, $availableTranslations)) {
            return $availableTranslations[$userLocaleCode]->getSlug();
        }

        // Try to get it from the default locale of the channel
        if (array_key_exists($defaultChannelLocaleCode, $availableTranslations)) {
            return $availableTranslations[$defaultChannelLocaleCode]->getSlug();
        }

        // Just return the first configured locale
        $firstTranslation = reset($availableTranslations);

        return $firstTranslation->getSlug();
    }
}
