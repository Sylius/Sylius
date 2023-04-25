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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneCurrency;
use Sylius\Bundle\CoreBundle\ShopFixtures\Symfony\Messenger\CommandBusInterface;

final class DefaultCurrenciesStory implements DefaultCurrenciesStoryInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function create(): void
    {
        foreach ($this->getCurrencyCodes() as $currencyCode) {
            $this->commandBus->dispatch((new CreateOneCurrency())->withCode($currencyCode));
        }
    }

    private function getCurrencyCodes(): array
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
