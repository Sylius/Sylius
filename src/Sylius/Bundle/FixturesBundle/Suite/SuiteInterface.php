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

namespace Sylius\Bundle\FixturesBundle\Suite;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface SuiteInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @see \Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface
     *
     * @return \Traversable Fixtures as keys, options as values
     */
    public function getFixtures();

    /**
     * @see \Sylius\Bundle\FixturesBundle\Listener\ListenerInterface
     *
     * @return \Traversable Listeners as keys, options as values
     */
    public function getListeners();
}
