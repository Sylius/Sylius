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

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story;

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\LocaleFactory;
use Zenstruck\Foundry\Factory;
use Zenstruck\Foundry\Story;

final class DefaultLocalesStory extends Story
{
    public function build(): void
    {
        Factory::delayFlush(function() {
            foreach ($this->getLocaleCodes() as $currencyCode) {
                LocaleFactory::new()->withCode($currencyCode)->create();
            }
        });
    }

    private function getLocaleCodes(): array
    {
        return [
            'en_US',
            'de_DE',
            'fr_FR',
            'pl_PL',
            'es_ES',
            'es_MX',
            'pt_PT',
            'zh_CN',
        ];
    }
}
