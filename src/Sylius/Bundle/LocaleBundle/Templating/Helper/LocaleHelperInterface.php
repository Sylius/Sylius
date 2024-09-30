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

namespace Sylius\Bundle\LocaleBundle\Templating\Helper;

use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Symfony\Component\Templating\Helper\HelperInterface;

trigger_deprecation(
    'sylius/locale-bundle',
    '1.14',
    'The "%s" interface is deprecated, use "%s" instead.',
    LocaleHelperInterface::class,
    LocaleConverterInterface::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. Use {@see \Sylius\Component\Locale\Converter\LocaleConverterInterface} instead. */
interface LocaleHelperInterface extends HelperInterface
{
    /**
     * @param string $code The code to be converted to a name
     * @param string|null $localeCode The locale that the returned name should be in
     */
    public function convertCodeToName(string $code, ?string $localeCode = null): ?string;
}
