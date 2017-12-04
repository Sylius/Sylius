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

use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Listener\ListenerInterface;

interface SuiteInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return iterable|FixtureInterface[] Fixtures as keys, options as values
     */
    public function getFixtures(): iterable;

    /**
     * @see \Sylius\Bundle\FixturesBundle\Listener\ListenerInterface
     *
     * @return iterable|ListenerInterface[] Listeners as keys, options as values
     */
    public function getListeners(): iterable;
}
