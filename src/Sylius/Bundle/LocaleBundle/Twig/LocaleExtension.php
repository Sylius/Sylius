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

namespace Sylius\Bundle\LocaleBundle\Twig;

use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class LocaleExtension extends AbstractExtension
{
    private LocaleHelperInterface $localeHelper;

    public function __construct(LocaleHelperInterface $localeHelper)
    {
        $this->localeHelper = $localeHelper;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_locale_name', [$this->localeHelper, 'convertCodeToName']),
            new TwigFilter('sylius_locale_country', [$this, 'getCountryCode']),
        ];
    }

    public function getCountryCode(string $locale): ?string
    {
        return \Locale::getRegion($locale);
    }
}
