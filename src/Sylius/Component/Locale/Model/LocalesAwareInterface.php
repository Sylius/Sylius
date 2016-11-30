<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface LocalesAwareInterface
{
    /**
     * @return Collection|LocaleInterface[]
     */
    public function getLocales();

    /**
     * @param LocaleInterface $locale
     *
     * @return bool
     */
    public function hasLocale(LocaleInterface $locale);

    /**
     * @param LocaleInterface $locale
     */
    public function addLocale(LocaleInterface $locale);

    /**
     * @param LocaleInterface $locale
     */
    public function removeLocale(LocaleInterface $locale);
}
