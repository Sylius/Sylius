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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\GetLocalesTrait;
use Sylius\Component\Product\Model\ProductAttributeInterface;

final class ProductAttributeUpdater implements ProductAttributeUpdaterInterface
{
    use GetLocalesTrait;

    public function __construct(private LocaleFactoryInterface $localeFactory)
    {
    }

    public function update(ProductAttributeInterface $productAttribute, array $attributes): void
    {
        $productAttribute->setCode($attributes['code']);
        $productAttribute->setTranslatable($attributes['translatable']);

        foreach ($this->getLocales() as $localeCode) {
            $productAttribute->setCurrentLocale($localeCode);
            $productAttribute->setFallbackLocale($localeCode);

            $productAttribute->setName($attributes['name']);
        }

        $productAttribute->setConfiguration($attributes['configuration']);
    }
}
