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
interface SlugGeneratorInterface
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function generate($name);
}
