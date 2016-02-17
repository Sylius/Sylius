<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Provider;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface ProvinceNamingProviderInterface
{
    /**
     * @param string $provinceCode
     *
     * @return string
     */
    public function getName($provinceCode);

    /**
     * @param string $provinceCode
     *
     * @return string
     */
    public function getAbbreviation($provinceCode);
}
