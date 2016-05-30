<?php

namespace Sylius\Bundle\FixturesBundle\Suite;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface SuiteFactoryInterface
{
    /**
     * @param string $name
     * @param array $configuration
     *
     * @return SuiteInterface
     */
    public function createSuite($name, array $configuration);
}
