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

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FallbackLocalesProvider implements FallbackLocalesProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function computeFallbackLocales($locale, array $baseFallbackLocales)
    {
        $localeWithoutModifier = strstr($locale, '@', true) ?: $locale;
        $localeModifier = substr(strrchr($locale, '@'), 1) ?: null;

        $fallbackLocales = [];
        foreach (array_merge([$localeWithoutModifier], $baseFallbackLocales) as $fallbackLocale) {
            foreach ($this->generateLocalesWithModifier($fallbackLocale, $localeModifier) as $generatedLocale) {
                if ($generatedLocale === $locale) {
                    continue;
                }

                $fallbackLocales[] = $generatedLocale;
            }
        }

        return array_unique($fallbackLocales);
    }

    /**
     * @param string $localeWithoutModifier
     * @param string|null $modifier
     *
     * @return \Generator
     */
    private function generateLocalesWithModifier($localeWithoutModifier, $modifier = null)
    {
        foreach ($this->generateLocales($localeWithoutModifier) as $generatedLocale) {
            if (null !== $modifier) {
                yield $generatedLocale . '@' . $modifier;
            }

            yield $generatedLocale;
        }
    }

    /**
     * @param string $localeWithoutModifier
     *
     * @return \Generator
     */
    private function generateLocales($localeWithoutModifier)
    {
        yield $localeWithoutModifier;

        if (false !== strrpos($localeWithoutModifier, '_')) {
            yield substr($localeWithoutModifier, 0, -strlen(strrchr($localeWithoutModifier, '_')));
        }
    }
}
