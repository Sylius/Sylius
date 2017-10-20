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

namespace Sylius\Bundle\LocaleBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\HelperInterface;

interface LocaleHelperInterface extends HelperInterface
{
    /**
     * @param string $localeCode
     *
     * @return string|null
     */
    public function convertCodeToName(string $localeCode): ?string;
}
