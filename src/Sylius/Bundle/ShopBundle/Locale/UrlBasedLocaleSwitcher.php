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

namespace Sylius\Bundle\ShopBundle\Locale;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UrlBasedLocaleSwitcher implements LocaleSwitcherInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function handle(Request $request, string $localeCode): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('sylius_shop_homepage', ['_locale' => $localeCode]));
    }
}
