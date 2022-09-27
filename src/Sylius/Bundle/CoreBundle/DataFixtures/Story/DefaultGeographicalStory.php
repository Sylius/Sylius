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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultGeographicalStory extends Story implements DefaultGeographicalStoryInterface
{
    public function __construct(
        private CountryFactoryInterface $countryFactory,
        private ZoneFactoryInterface $zoneFactory,
    ) {
    }

    public function build(): void
    {
        foreach ($this->getDefaultCountryCodes() as $countryCode) {
            $this->countryFactory::new()->withCode($countryCode)->create();
        }

        $this->zoneFactory::new()
            ->withName('United States of America')
            ->withCountries(['US'])
            ->create()
        ;

        $this->zoneFactory::new()
            ->withName('Rest of the World')
            ->withCountries($this->getDefaultCountryCodes())
            ->create()
        ;
    }

    public function getDefaultCountryCodes(): array
    {
        return [
            'FR',
            'DE',
            'AU',
            'CA',
            'MX',
            'NZ',
            'PT',
            'ES',
            'CN',
            'GB',
            'PL',
        ];
    }
}
