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

namespace Sylius\Component\Addressing\Provider;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

interface ZoneCountriesProviderInterface
{
    /**
     * @return array|CountryInterface[]
     */
    public function getCountriesInWhichZoneOperates(ZoneInterface $zone): array;
}
