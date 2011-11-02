<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Sorting\ORM;

use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\SalesBundle\Sorting\SorterInterface;

/**
 * Default ORM sorter.
 * Sorts order entities.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderSorter extends ContainerAware implements SorterInterface
{
    public function sort($sortable)
    {
        if (!$sortable instanceof QueryBuilder) {
            throw new InvalidArgumentException('Default sorter supports only "Doctrine\\ORM\\QueryBuilder" as sortable argument.');
        }
        
        $request = $this->container->get('request');
        
        if (null === $sortProperty = $request->query->get('sort', null)) {
            
            return;
        }
        
        $sortOrder = $request->query->get('order', 'ASC');
        
        if (!in_array($sortOrder, array('ASC', 'DESC'))) {
            
            return;
        }
        
        $orderClass = $this->container->getParameter('sylius_sales.model.order.class');
        $reflectionClass = new \ReflectionClass($orderClass);
        
        if (!in_array($sortProperty, array_keys($reflectionClass->getDefaultProperties()))) {
            
            return;
        }
        
        /** @var QueryBuilder */
        $sortable->orderBy('o.' . $sortProperty, $sortOrder);
    }
}
