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

namespace Sylius\Bundle\CoreBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultChannelsStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultCurrenciesStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultGeographicalStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultLocalesStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultMenuTaxonStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultPaymentMethodsStoryInterface;

final class ShopConfigurationFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private DefaultLocalesStoryInterface $defaultLocalesStory,
        private DefaultCurrenciesStoryInterface $defaultCurrenciesStory,
        private DefaultGeographicalStoryInterface $defaultGeographicalStory,
        private DefaultMenuTaxonStoryInterface $defaultMenuTaxonStory,
        private DefaultChannelsStoryInterface $defaultChannelsStory,
        private DefaultPaymentMethodsStoryInterface $defaultPaymentMethodsStory,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->defaultLocalesStory->build();
        $this->defaultCurrenciesStory->build();
        $this->defaultGeographicalStory->build();
        $this->defaultMenuTaxonStory->build();
        $this->defaultChannelsStory->build();
        $this->defaultPaymentMethodsStory->build();
    }

    public static function getGroups(): array
    {
        return ['shop_configuration'];
    }
}
