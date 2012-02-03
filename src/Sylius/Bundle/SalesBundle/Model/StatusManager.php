<?php

namespace Sylius\Bundle\SalesBundle\Model;

abstract class StatusManager implements StatusManagerInterface
{
    protected $class;

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