<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Services;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface DefaultCountriesFactoryInterface
{
    /**
     * @param array $countryCodes
     */
    public function create(array $countryCodes);
}
