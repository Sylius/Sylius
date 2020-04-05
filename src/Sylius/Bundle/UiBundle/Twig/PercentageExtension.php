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

namespace Sylius\Bundle\UiBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PercentageExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_percentage', [$this, 'getPercentage']),
        ];
    }

    public function getPercentage(float $number): string
    {
        $percentage = $number * 100;

        return $percentage . ' %';
    }
}
