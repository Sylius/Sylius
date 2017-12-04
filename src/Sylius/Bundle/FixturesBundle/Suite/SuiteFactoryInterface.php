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

interface SuiteFactoryInterface
{
    /**
     * @param string $name
     * @param array $configuration
     *
     * @return SuiteInterface
     */
    public function createSuite(string $name, array $configuration): SuiteInterface;
}
