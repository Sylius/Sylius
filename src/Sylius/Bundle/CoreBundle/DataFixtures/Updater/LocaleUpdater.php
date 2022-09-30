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

use Sylius\Component\Locale\Model\LocaleInterface;

final class LocaleUpdater implements LocaleUpdaterInterface
{
    public function update(LocaleInterface $locale, array $attributes): void
    {
        $locale->setCode($attributes['code']);
    }
}
