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

namespace Sylius\Bundle\ThemeBundle\Translation;

use Sylius\Bundle\ThemeBundle\Translation\Provider\Loader\TranslatorLoaderProviderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\TranslatorResourceProviderInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator as BaseTranslator;

final class Translator extends BaseTranslator implements WarmableInterface
{
    /**
     * @var array
     */
    protected $options = [
        'cache_dir' => null,
        'debug' => false,
    ];

    /**
     * @var TranslatorLoaderProviderInterface
     */
    private $loaderProvider;

    /**
     * @var TranslatorResourceProviderInterface
     */
    private $resourceProvider;

    /**
     * @var bool
     */
    private $resourcesLoaded = false;

    public function __construct(
        TranslatorLoaderProviderInterface $loaderProvider,
        TranslatorResourceProviderInterface $resourceProvider,
        MessageSelector $messageSelector,
        string $locale,
        array $options = []
    ) {
        $this->assertOptionsAreKnown($options);

        $this->loaderProvider = $loaderProvider;
        $this->resourceProvider = $resourceProvider;

        $this->options = array_merge($this->options, $options);
        if (null !== $this->options['cache_dir'] && $this->options['debug']) {
            $this->addResources();
        }

        parent::__construct($locale, $messageSelector, $this->options['cache_dir'], $this->options['debug']);
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir): void
    {
        // skip warmUp when translator doesn't use cache
        if (null === $this->options['cache_dir']) {
            return;
        }

        $locales = array_merge(
            $this->getFallbackLocales(),
            [$this->getLocale()],
            $this->resourceProvider->getResourcesLocales()
        );
        foreach (array_unique($locales) as $locale) {
            // reset catalogue in case it's already loaded during the dump of the other locales.
            if (isset($this->catalogues[$locale])) {
                unset($this->catalogues[$locale]);
            }

            $this->loadCatalogue($locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeCatalogue($locale): void
    {
        $this->initialize();

        parent::initializeCatalogue($locale);
    }

    /**
     * {@inheritdoc}
     */
    protected function computeFallbackLocales($locale): array
    {
        $themeModifier = $this->getLocaleModifier($locale);
        $localeWithoutModifier = $this->getLocaleWithoutModifier($locale, $themeModifier);

        $computedFallbackLocales = parent::computeFallbackLocales($locale);
        array_unshift($computedFallbackLocales, $localeWithoutModifier);

        $fallbackLocales = [];
        foreach (array_diff($computedFallbackLocales, [$locale]) as $computedFallback) {
            $fallback = $computedFallback . $themeModifier;
            if (null !== $themeModifier && $locale !== $fallback) {
                $fallbackLocales[] = $fallback;
            }

            $fallbackLocales[] = $computedFallback;
        }

        return array_unique($fallbackLocales);
    }

    private function getLocaleModifier(string $locale): string
    {
        $modifier = strrchr($locale, '@');

        return $modifier ?: '';
    }

    private function getLocaleWithoutModifier(string $locale, string $modifier): string
    {
        return str_replace($modifier, '', $locale);
    }

    private function initialize(): void
    {
        $this->addResources();
        $this->addLoaders();
    }

    private function addResources(): void
    {
        if ($this->resourcesLoaded) {
            return;
        }

        $resources = $this->resourceProvider->getResources();
        foreach ($resources as $resource) {
            $this->addResource(
                $resource->getFormat(),
                $resource->getName(),
                $resource->getLocale(),
                $resource->getDomain()
            );
        }

        $this->resourcesLoaded = true;
    }

    private function addLoaders(): void
    {
        $loaders = $this->loaderProvider->getLoaders();
        foreach ($loaders as $alias => $loader) {
            $this->addLoader($alias, $loader);
        }
    }

    private function assertOptionsAreKnown(array $options): void
    {
        if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new \InvalidArgumentException(sprintf('The Translator does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }
    }
}
