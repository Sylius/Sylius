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
 * Interface implemented by objects related to multiple locales
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface LocalesAwareInterface
{
    /**
     * @return Collection|LocaleInterface[]
     */
    public function getLocales();

    /**
     * @param Collection $collection
     */
    public function setLocales(Collection $collection);

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
