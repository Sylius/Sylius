<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Provider;

use Sylius\Component\Locale\Context\LocaleNotFoundException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface LocaleProviderInterface
{
    /**
     * @return string
     *
     * @throws LocaleNotFoundException
     */
    public function getDefaultLocaleCode();

    /**
     * @return string[]
     *
     * @throws LocaleNotFoundException
     */
    public function getAvailableLocalesCodes();
}
