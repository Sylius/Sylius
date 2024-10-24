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

namespace Sylius\Component\Locale\Context;

final class ImmutableLocaleContext implements LocaleContextInterface
{
    public function __construct(private string $localeCode)
    {
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }
}
