<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Processor;

use Sylius\Bundle\SalesBundle\Processor\Operation\OperationInterface;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Order processor.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Processor
{
    /**
     * Processor operation.
     * 
     * @var array
     */
    private $operations = array();
    
    /**
     * Processes order. Calls all operations.
     * 
     * @param OrderInterface $order
     */
    public function processOrder(OrderInterface $order)
    {
        foreach ($this->operations as $operation) {
            $operation->process($order);
        }
        
        return $order;
    }
    
    /**
     * Registers processor.
     * 
     * @var OperationInterface $operation
     */
    public function registerOperation(OperationInterface $operation)
    {
        $this->operations = $operation;
    }
    
    /**
     * Unergisters operation.
     * 
     * @param OperationInterface $operation
     */
    public function unregisterOperation(OperationInterface $operation)
    {
    }
}