<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Model;

/**
 * Inventory manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class InventoryUnitManager implements InventoryUnitManagerInterface
{
    /**
     * Inventory unit class.
     *
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @var string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * Returns FQCN of inventory unit.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
