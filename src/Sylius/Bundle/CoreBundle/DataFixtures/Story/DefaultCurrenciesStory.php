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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CurrencyFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultCurrenciesStory extends Story implements DefaultCurrenciesStoryInterface
{
    public function __construct(private CurrencyFactoryInterface $currencyFactory)
    {
    }

    public function build(): void
    {
        foreach ($this->getDefaultCurrencyCodes() as $localeCode) {
            $this->currencyFactory::new()->withCode($localeCode)->create();
        }
    }

    public function getDefaultCurrencyCodes(): array
    {
        return [
            'EUR',
            'USD',
            'PLN',
            'CAD',
            'CNY',
            'NZD',
            'GBP',
            'AUD',
            'MXN',
        ];
    }
}
