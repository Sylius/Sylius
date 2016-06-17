<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Fixture;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface FixtureRegistryInterface
{
    /**
     * @param string $name
     *
     * @return FixtureInterface
     *
     * @throws FixtureNotFoundException
     */
    public function getFixture($name);

    /**
     * @return FixtureInterface[] Name indexed
     */
    public function getFixtures();
}
