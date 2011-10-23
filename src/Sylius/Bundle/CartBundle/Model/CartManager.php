<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Model;

/**
 * Base class for cart model manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class CartManager implements CartManagerInterface
{
    /**
     * FQCN of cart model.
     * 
     * @var string
     */
    protected $class;
    
    /**
     * Constructor.
     * 
     * @param string $class FQCN of cart model
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
