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

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\Templating\Helper\PriceHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class PriceExtension extends AbstractExtension
{
    public function __construct(private PriceHelper $helper)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_calculate_price', [$this->helper, 'getPrice']),
            new TwigFilter('sylius_calculate_original_price', [$this->helper, 'getOriginalPrice']),
            new TwigFilter('sylius_has_discount', [$this->helper, 'hasDiscount']),
            new TwigFilter('sylius_has_lowest_price', [$this->helper, 'hasLowestPriceBeforeDiscount']),
            new TwigFilter('sylius_calculate_lowest_price', [$this->helper, 'getLowestPriceBeforeDiscount']),
        ];
    }
}
