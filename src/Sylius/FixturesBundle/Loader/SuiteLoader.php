<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\FixturesBundle\Loader;

use Sylius\FixturesBundle\Fixture\FixtureInterface;
use Sylius\FixturesBundle\Suite\SuiteInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SuiteLoader implements SuiteLoaderInterface
{
    /**
     * @var FixtureLoaderInterface
     */
    private $fixtureLoader;

    /**
     * @param FixtureLoaderInterface $fixtureLoader
     */
    public function __construct(FixtureLoaderInterface $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(SuiteInterface $suite)
    {
        /**
         * @var FixtureInterface $fixture
         * @var array $fixtureOptions
         */
        foreach ($suite->getFixtures() as $fixture => $fixtureOptions) {
            $this->fixtureLoader->load($suite, $fixture, $fixtureOptions);
        }
    }
}
