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

interface FixtureLoaderInterface
{
    /**
     * @param SuiteInterface $suite
     * @param FixtureInterface $fixture
     * @param array $options
     */
    public function load(SuiteInterface $suite, FixtureInterface $fixture, array $options): void;
}
