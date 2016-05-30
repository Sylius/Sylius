<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Suite;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface SuiteInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return \Traversable Fixtures as keys, options as values
     */
    public function getFixtures();
}
