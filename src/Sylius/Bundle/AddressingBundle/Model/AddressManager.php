<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Model;

/**
 * Address manager model.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
abstract class AddressManager implements AddressManagerInterface
{
    /**
     * Address model class.
     * 
     * @var string
     */
    protected $class;
    
    /**
     * Constructor.
     * 
     * @param string $class The address model class
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
    
    /**
     * {@inheritdoc}
     */
    public function setClass($class)
    {
        $this->class = $class;
    }
}
