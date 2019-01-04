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

namespace Sylius\Bundle\FixturesBundle\Listener;

use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

final class FixtureEvent
{
    /** @var SuiteInterface */
    private $suite;

    /** @var FixtureInterface */
    private $fixture;

    /** @var array */
    private $fixtureOptions;

    public function __construct(SuiteInterface $suite, FixtureInterface $fixture, array $fixtureOptions)
    {
        $this->suite = $suite;
        $this->fixture = $fixture;
        $this->fixtureOptions = $fixtureOptions;
    }

    public function suite(): SuiteInterface
    {
        return $this->suite;
    }

    public function fixture(): FixtureInterface
    {
        return $this->fixture;
    }

    public function fixtureOptions(): array
    {
        return $this->fixtureOptions;
    }
}
