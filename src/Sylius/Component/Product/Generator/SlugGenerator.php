<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Generator;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class SlugGenerator implements SlugGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate($name)
    {
        return str_replace([': ', '_', ':', ';', '-', ' '], '-', str_replace(['\'', '"'], '', strtolower($name)));
    }
}
