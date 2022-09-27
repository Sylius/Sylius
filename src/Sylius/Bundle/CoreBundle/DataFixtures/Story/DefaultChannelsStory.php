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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultChannelsStory extends Story implements DefaultChannelsStoryInterface
{
    public function __construct(
        private ChannelFactoryInterface $channelFactory,
        private string $basedLocaleCode,
        private string $fixturesHostname,
        private ?string $fixturesThemeName,
    ) {
    }

    public function build(): void
    {
        $this->channelFactory::new()
            ->withName('Fashion Web Store')
            ->withCode('FASHION_WEB')
            ->withLocales([$this->basedLocaleCode])
            ->withCurrencies(['USD'])
            ->enabled()
            ->withHostname($this->fixturesHostname)
            ->withThemeName($this->fixturesThemeName)
            ->withShopBillingData([
                'company' => 'Sylius',
                'tax_id' => '0001112222',
                'country_code' => 'US',
                'street' => 'Test St. 15',
                'city' => 'eCommerce Town',
                'postcode' => '00 33 22',
            ])
            ->withMenuTaxon('MENU_CATEGORY')
            ->withContactPhoneNumber('+41 123 456 789')
            ->withContactEmail('contact@example.com')
            ->create()
        ;
    }
}
