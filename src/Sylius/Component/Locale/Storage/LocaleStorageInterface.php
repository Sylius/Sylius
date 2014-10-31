<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Storage;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface LocaleStorageInterface
{
    /**
     * Returns current locale from storage or the default one.
     *
     * @param string $defaultLocale
     *
     * @return string
     */
    public function getCurrentLocale($defaultLocale);

    /**
     * Sets current locale in storage.
     *
     * @param string $locale
     */
    public function setCurrentLocale($locale);
}
