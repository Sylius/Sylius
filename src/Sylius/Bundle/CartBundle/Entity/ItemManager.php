<?php

namespace Sylius\Bundle\CartBundle\Entity;

use Doctrine\ORM\EntityManager;
use Sylius\Bundle\CartBundle\Model\ItemInterface;
use Sylius\Bundle\CartBundle\Model\ItemManager as BaseItemManager;

class ItemManager extends BaseItemManager
{
    /**
     * Entity Manager.
     * 
     * @var EntityManager
     */
    protected $em;
    
    /**
     * Items repository.
     */
    protected $repository;
    
    /**
     * Constructor.
     * 
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, $class)
    {
        parent::__construct($class);
        
        $this->em = $em; 
        $this->repository = $this->em->getRepository($class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function createItem()
    {
        $class = $this->getClass();
        return new $class;
    }
    
    /**
     * {@inheritdoc}
     */
    public function persistItem(ItemInterface $cart)
    {
        $this->em->persist($cart);
        $this->em->flush();
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeItem(ItemInterface $cart)
    {
        $this->em->remove($cart);
        $this->em->flush();
    }
    
    /**
     * {@inheritdoc}
     */
    public function findItem($id)
    {
        return $this->repository->find($id);
    }
    
    /**
     * {@inheritdoc}
     */
    public function findItemBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
    
    /**
     * {@inheritdoc}
     */
    public function findItems()
    {
        return $this->repository->findAll();
    }
    
    /**
     * {@inheritdoc}
     */
    public function findItemsBy(array $criteria)
    {
        return $this->findBy($criteria);
    }
}
