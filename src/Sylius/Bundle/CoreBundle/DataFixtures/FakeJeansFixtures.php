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
use Sylius\Bundle\CoreBundle\DataFixtures\Story\FakeJeansStoryInterface;

final class FakeJeansFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private FakeJeansStoryInterface $fakeJeansStory) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->fakeJeansStory->build();
    }

    public static function getGroups(): array
    {
        return ['fake', 'fake_products', 'fake_jeans'];
    }
}
