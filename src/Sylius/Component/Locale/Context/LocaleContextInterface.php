<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Context;

/**
 * Interface to be implemented by the service providing the currently used
 * locale.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface LocaleContextInterface
{
    // Key used to store the locale in storage.
    const STORAGE_KEY = '_sylius.locale';

    /**
     * Get the default locale.
     *
     * @return string
     */
    public function getDefaultLocale();

    /**
     * Get the currently active locale.
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set the currently active locale.
     *
     * @param string $locale
     */
    public function setLocale($locale);
}
