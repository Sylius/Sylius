<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Entity;

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Model\CartManager as BaseCartManager;
use Symfony\Component\HttpFoundation\Session;
use Doctrine\ORM\EntityManager;

/**
 * Cart manager.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartManager extends BaseCartManager
{
    /**
     * Entity Manager.
     * 
     * @var EntityManager
     */
    protected $entityManager;
    
    /**
     * Carts repository.
     */
    protected $repository;
    
    /**
     * Constructor.
     * 
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, $class)
    {
        parent::__construct($class);
        
        $this->entityManager = $entityManager; 
        $this->repository = $this->entityManager->getRepository($class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function createCart()
    {
        $class = $this->getClass();
        return new $class;
    }
    
    /**
     * {@inheritdoc}
     */
    public function persistCart(CartInterface $cart)
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeCart(CartInterface $cart)
    {
        $this->entityManager->remove($cart);
        $this->entityManager->flush();
    }
    
    public function flushCarts()
    {
        $expiredCarts = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from($this->getClass(), 'c')
            ->where('c.locked = false AND c.expiresAt < ?1')
            ->setParameter(1, new \DateTime)
            ->getQuery()
            ->getResult()
        ;
        
        foreach ($expiredCarts as $cart) {
            $this->removeCart($cart);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function findCart($id)
    {
        return $this->repository->find($id);
    }
    
    /**
     * {@inheritdoc}
     */
    public function findCartBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
    
    /**
     * {@inheritdoc}
     */
    public function findCarts()
    {
        return $this->repository->findAll();
    }
    
    /**
     * {@inheritdoc}
     */
    public function findCartsBy(array $criteria)
    {
        return $this->findBy($criteria);
    }
}
