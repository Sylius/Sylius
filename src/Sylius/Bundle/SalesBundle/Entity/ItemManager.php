<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Entity;

use Sylius\Bundle\SalesBundle\Model\ItemInterface;

use Sylius\Bundle\SalesBundle\Model\ItemManager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ItemManager extends ItemManager
{
    /**
     * Entity manager.
     * 
     * @var EntityManager
     */
    protected $entityManager;
    
    /**
     * Entity repository.
     * 
     * @var EntityRepository
     */
    protected $repository;
    
    /**
     * Constructor.
     * 
     * @param EntityManager	$entityManager
     * @param string		$class
     */
    public function __construct(EntityManager $entityManager, $class)
    {
        parent::__construct($class);
        
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository($this->getClass());
    }
    
    /**
     * {@inheritdoc}
     */
    public function createItem()
    {
        $class = $this->getClass();
        return new $class;
    }
    
    public function persistItem(ItemInterface $order)
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
    
    public function removeItem(ItemInterface $order)
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }
    
    public function findItem($id)
    {
        return $this->repository->find($id);
    }
    
    public function findItemBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
    
    public function findItems()
    {
        return $this->repository->findAll();
    }
    
    public function findItemsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }
}
