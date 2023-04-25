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

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Story;

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneCountry;
use Sylius\Bundle\CoreBundle\ShopFixtures\Symfony\Messenger\CommandBusInterface;

final class DefaultGeographicalStory implements DefaultGeographicalStoryInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function create(): void
    {
        foreach ($this->getCountryCodes() as $countryCode) {
            $this->commandBus->dispatch((new CreateOneCountry())->withCode($countryCode));
        }
    }

    private function getCountryCodes(): array
    {
        return [
            'US',
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
