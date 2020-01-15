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

use Sylius\Component\Addressing\Provider\ProvinceNamingProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ProvinceNamingExtension extends AbstractExtension
{
    /** @var ProvinceNamingProviderInterface */
    private $provinceNamingProvider;

    public function __construct(ProvinceNamingProviderInterface $provinceNamingProvider)
    {
        $this->provinceNamingProvider = $provinceNamingProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_province_name', [$this->provinceNamingProvider, 'getName']),
            new TwigFilter('sylius_province_abbreviation', [$this->provinceNamingProvider, 'getAbbreviation']),
        ];
    }
}
