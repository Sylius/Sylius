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
use Sylius\Bundle\CoreBundle\DataFixtures\Story\FakeCatalogPromotionsStory;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\FakeCatalogPromotionsStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\FakeJeansStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\FakePromotionsStoryInterface;

final class FakePromotionsFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private FakePromotionsStoryInterface $fakePromotionsStory,
        private FakeCatalogPromotionsStoryInterface $fakeCatalogPromotionsStory,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->fakePromotionsStory->build();
        $this->fakeCatalogPromotionsStory->build();
    }

    public static function getGroups(): array
    {
        return ['fake', 'fake_promotions'];
    }
}
