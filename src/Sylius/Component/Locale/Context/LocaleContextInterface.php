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
    /**
     * @return string
     */
    public function getDefaultLocale();

    /**
     * @return string
     */
    public function getCurrentLocale();

    /**
     * @param string $locale
     */
    public function setCurrentLocale($locale);
}
