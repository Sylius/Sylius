<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\FixturesBundle\Listener;

use Sylius\FixturesBundle\Suite\SuiteInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SuiteEvent
{
    /**
     * @var SuiteInterface
     */
    private $suite;

    /**
     * @param SuiteInterface $suite
     */
    public function __construct(SuiteInterface $suite)
    {
        $this->suite = $suite;
    }

    /**
     * @return SuiteInterface
     */
    public function suite()
    {
        return $this->suite;
    }
}
