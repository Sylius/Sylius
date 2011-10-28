<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

/**
 * Base class for order item model manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class ItemManager implements ItemManagerInterface
{
    /**
     * FQCN for order item model.
     * 
     * @var string
     */
    protected $class;
    
    /**
     * Constructor.
     * 
     * @param string $class The FQCN for order item model
     */
    public function __construct($class)
    {
        $this->class = $class;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }
}
