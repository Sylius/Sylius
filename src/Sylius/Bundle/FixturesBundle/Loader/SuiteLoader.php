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

namespace Sylius\Bundle\FixturesBundle\Loader;

use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

final class SuiteLoader implements SuiteLoaderInterface
{
    /** @var FixtureLoaderInterface */
    private $fixtureLoader;

    public function __construct(FixtureLoaderInterface $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(SuiteInterface $suite): void
    {
        /** @var FixtureInterface $fixture */
        /** @var array $fixtureOptions */
        foreach ($suite->getFixtures() as $fixture => $fixtureOptions) {
            $this->fixtureLoader->load($suite, $fixture, $fixtureOptions);
        }
    }
}
