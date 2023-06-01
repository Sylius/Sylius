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

namespace Sylius\Bundle\LocaleBundle\Remover;

use Sylius\Bundle\LocaleBundle\Checker\Exception\LocaleIsUsedException;
use Sylius\Component\Locale\Context\LocaleNotFoundException;

interface LocaleRemoverInterface
{
    /**
     * @throws LocaleNotFoundException
     * @throws LocaleIsUsedException
     */
    public function removeById(int $id): void;

    /**
     * @throws LocaleNotFoundException
     * @throws LocaleIsUsedException
     */
    public function removeByCode(string $localeCode): void;
}
