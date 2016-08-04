<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Translation\Provider\Locale;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface FallbackLocalesProviderInterface
{
    /**
     * @param string $locale
     * @param array $baseFallbackLocales
     *
     * @return array
     */
    public function computeFallbackLocales($locale, array $baseFallbackLocales);
}
