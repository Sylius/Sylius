<?php

namespace Sylius\Bundle\SalesBundle\Sorting\ORM;

use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\SalesBundle\Sorting\SorterInterface;

class StatusSorter extends ContainerAware implements SorterInterface
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

        $sortOrder = $request->query->get('status', 'ASC');
        
        if (!in_array($sortOrder, array('ASC', 'DESC'))) {
            
            return;
        }

        $orderClass = $this->container->getParameter('sylius_sales.model.status.class');
        $reflectionClass = new \ReflectionClass($orderClass);

        if (!in_array($sortProperty, array_keys($reflectionClass->getDefaultProperties()))) {
            
            return;
        }

        /** @var QueryBuilder */
        $sortable->orderBy('s.' . $sortProperty, $sortOrder);
    }

    public function getOrder()
    {
        $sortOrder = $this->container->get('request')->query->get('status', 'ASC');
        
        if (!in_array($sortOrder, array('ASC', 'DESC'))) {
        
            return;
        }
        
        return ($sortOrder == 'ASC') ? 'DESC' : 'ASC';
    }
}