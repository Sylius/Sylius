<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UiBundle\Twig;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class PercentageExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_percentage', [$this, 'getPercentage']),
        ];
    }

    /**
     * @param float $number
     *
     * @return string
     */
    public function getPercentage($number)
    {
        $percentage = $number * 100;

        return $percentage.' %';
    }
}
