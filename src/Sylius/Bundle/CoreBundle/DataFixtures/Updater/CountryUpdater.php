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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Sylius\Component\Addressing\Model\CountryInterface;

final class CountryUpdater implements CountryUpdaterInterface
{
    public function update(CountryInterface $country, array $attributes): void
    {
        $country->setCode($attributes['code']);
        $country->setEnabled($attributes['enabled']);
    }
}
