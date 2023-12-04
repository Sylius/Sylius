<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
     * @return Collection<array-key, LocaleInterface>
     */
    public function getLocales(): Collection;

    public function hasLocale(LocaleInterface $locale): bool;

    public function addLocale(LocaleInterface $locale): void;

    public function removeLocale(LocaleInterface $locale): void;
}
