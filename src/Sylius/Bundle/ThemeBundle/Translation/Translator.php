<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation;

use Sylius\Bundle\ThemeBundle\Translation\Provider\Loader\TranslatorLoaderProviderInterface;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Resource\TranslatorResourceProviderInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator as BaseTranslator;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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

    /**
     * @param TranslatorLoaderProviderInterface $loaderProvider
     * @param TranslatorResourceProviderInterface $resourceProvider
     * @param MessageSelector $messageSelector
     * @param string $locale
     * @param array $options
     */
    public function __construct(
        TranslatorLoaderProviderInterface $loaderProvider,
        TranslatorResourceProviderInterface $resourceProvider,
        MessageSelector $messageSelector,
        $locale,
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
    public function warmUp($cacheDir)
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
    protected function initializeCatalogue($locale)
    {
        $this->initialize();

        parent::initializeCatalogue($locale);
    }

    /**
     * {@inheritdoc}
     */
    protected function computeFallbackLocales($locale)
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

    /**
     * @param string $locale
     *
     * @return string
     */
    private function getLocaleModifier($locale)
    {
        $modifier = strrchr($locale, '@');

        return $modifier ?: '';
    }

    /**
     * @param string $locale
     * @param string $modifier
     *
     * @return string
     */
    private function getLocaleWithoutModifier($locale, $modifier)
    {
        return str_replace($modifier, '', $locale);
    }

    private function initialize()
    {
        $this->addResources();
        $this->addLoaders();
    }

    private function addResources()
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

    private function addLoaders()
    {
        $loaders = $this->loaderProvider->getLoaders();
        foreach ($loaders as $alias => $loader) {
            $this->addLoader($alias, $loader);
        }
    }

    /**
     * @param array $options
     */
    private function assertOptionsAreKnown(array $options)
    {
        if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new \InvalidArgumentException(sprintf('The Translator does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }
    }
}
