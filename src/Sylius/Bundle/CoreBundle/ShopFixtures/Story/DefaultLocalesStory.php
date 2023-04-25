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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneLocale;
use Sylius\Bundle\CoreBundle\ShopFixtures\Symfony\Messenger\CommandBusInterface;

final class DefaultLocalesStory implements DefaultLocalesStoryInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function create(): void
    {
        foreach ($this->getLocaleCodes() as $localeCode) {
            $this->commandBus->dispatch((new CreateOneLocale())->withCode($localeCode));
        }
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
