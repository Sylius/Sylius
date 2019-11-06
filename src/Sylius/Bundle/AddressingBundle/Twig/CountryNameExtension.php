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

namespace Sylius\Bundle\AddressingBundle\Twig;

use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\Intl\Intl;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CountryNameExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_country_name', [$this, 'translateCountryIsoCode']),
        ];
    }

    public function translateCountryIsoCode($country, ?string $locale = null): string
    {
        if ($country instanceof CountryInterface) {
            return Intl::getRegionBundle()->getCountryName($country->getCode(), $locale);
        }

        return Intl::getRegionBundle()->getCountryName($country, $locale);
    }
}
