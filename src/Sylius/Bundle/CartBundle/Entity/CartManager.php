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
    protected $em;
    
    /**
     * Carts repository.
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
        $this->em->persist($cart);
        $this->em->flush();
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeCart(CartInterface $cart)
    {
        $this->em->remove($cart);
        $this->em->flush();
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
