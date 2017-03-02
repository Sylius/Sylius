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

use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface FixtureInterface extends ConfigurationInterface
{
    /**
     * @param array $options
     * @param SuiteInterface $suite
     */
    public function load(array $options, SuiteInterface $suite);

    /**
     * @return string
     */
    public function getName();
}
