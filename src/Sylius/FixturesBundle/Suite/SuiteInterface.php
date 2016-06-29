<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\FixturesBundle\Suite;

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
     * @see \Sylius\FixturesBundle\Fixture\FixtureInterface
     *
     * @return \Traversable Fixtures as keys, options as values
     */
    public function getFixtures();

    /**
     * @see \Sylius\FixturesBundle\Listener\ListenerInterface
     *
     * @return \Traversable Listeners as keys, options as values
     */
    public function getListeners();
}
