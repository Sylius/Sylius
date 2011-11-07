<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Filtering\ORM;

use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\SalesBundle\Filtering\FilterInterface;

/**
 * Default ORM filter.
 * Filters order entities.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderFilter extends ContainerAware implements FilterInterface
{
    public function filter($filterable)
    {
        if (!$filterable instanceof QueryBuilder) {
            throw new InvalidArgumentException('Default filter supports only "Doctrine\\ORM\\QueryBuilder" as argument.');
        }
        
        $request = $this->container->get('request');
        $filters = $filters = $request->query->get('filters');
        if (null == $filters || !is_array($filters)) {
            
            return;
        }
        
        $orderClass = $this->container->getParameter('sylius_sales.model.order.class');
        $reflectionClass = new \ReflectionClass($orderClass);
        $properties = array_keys($reflectionClass->getDefaultProperties());

        $i = 0;
        foreach ($filters as $property => $filter) {
            if (in_array($property, $properties) && in_array($filter[1], array('>', '<', '=', '>=', '<='))) {
                /** @var QueryBuilder */
                $filterable
                    ->andWhere('o.'.$property . ' ' . $filter[1] . ' :PARAM' . $i)
                    ->setParameter('PARAM' . $i, $filter[0])
                ;
                
                $i++;
            }
        }
    }
}
