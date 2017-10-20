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

namespace Sylius\Bundle\ShopBundle\Locale;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

interface LocaleSwitcherInterface
{
    /**
     * @param Request $request
     * @param string $localeCode
     *
     * @return RedirectResponse
     */
    public function handle(Request $request, string $localeCode): RedirectResponse;
}
