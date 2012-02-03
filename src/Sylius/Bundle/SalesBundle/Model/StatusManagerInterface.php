<?php

namespace Sylius\Bundle\SalesBundle\Model;

interface StatusManagerInterface
{
    function getClass();
    
    function createStatus();
    
    function findStatus($id);
    
    function findStatusBy(array $criteria);
    
    function findStatuses();
    
    function findStatusesBy(array $criteria);
}
