<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\EventDispatcher\Event;

use Sylius\Bundle\FlowBundle\Setup\SetupInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Setup filter event.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterSetupEvent extends Event
{
    /**
     * Setup.
     *
     * @var SetupInterface
     */
    protected $setup;

    /**
     * Constructor.
     *
     * @param SetupInterface $setup
     */
    public function __construct(SetupInterface $setup)
    {
        $this->setup = $setup;
    }

    /**
     * Get setup.
     *
     * @return SetupInterface
     */
    public function getSetup()
    {
        return $this->setup;
    }
}
