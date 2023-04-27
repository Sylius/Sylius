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

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Doctrine\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\DefaultCurrenciesStory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\DefaultGeographicalStory;

final class ShopConfigurationFixture extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        DefaultCurrenciesStory::load();
        DefaultGeographicalStory::load();
    }

    public static function getGroups(): array
    {
        return ['shop_configuration'];
    }
}
