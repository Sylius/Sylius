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

use Symfony\Component\Templating\Helper\HelperInterface;

interface LocaleHelperInterface extends HelperInterface
{
    /**
     * @param string $code The code to be converted to a name
     * @param string|null $localeCode The locale that the returned name should be in
     */
    public function convertCodeToName(string $code, ?string $localeCode = null): ?string;
}
