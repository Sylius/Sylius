<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Locale\Model;

use Doctrine\Common\Collections\Collection;

interface LocalesAwareInterface
{
    /**
     * @return Collection|LocaleInterface[]
     */
    public function getLocales(): Collection;

    /**
     * @param LocaleInterface $locale
     *
     * @return bool
     */
    public function hasLocale(LocaleInterface $locale): bool;

    /**
     * @param LocaleInterface $locale
     */
    public function addLocale(LocaleInterface $locale): void;

    /**
     * @param LocaleInterface $locale
     */
    public function removeLocale(LocaleInterface $locale): void;
}
