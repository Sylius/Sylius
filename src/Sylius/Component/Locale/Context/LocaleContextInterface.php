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
 * @author Aram Alipoor <aram.alipoor@gmail.com>
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

    /**
     * @return string `rtl` or `ltr`
     */
    public function getCurrentDirection();

    /**
     * @return string
     *
     * @see \Sylius\Component\Locale\Calendars
     */
    public function getCurrentCalendar();
}
