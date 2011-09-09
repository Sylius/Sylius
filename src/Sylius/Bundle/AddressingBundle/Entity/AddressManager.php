<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Entity;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\AddressingBundle\Model\AddressManager as BaseAddressManager;

/**
 * Address entity manager.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class AddressManager extends BaseAddressManager
{
    /**
     * Entity manager.
     * 
     * @var EntityManager
     */
    protected $entityManager;
    
    /**
     * Address repository.
     * 
     * @var EntityRepository
     */
    protected $repository;
    
    /**
     * Constructor.
     * 
     * @param EntityManager $entityManager
     * @param string		$class
     */
    public function __construct(EntityManager $entityManager, $class)
    {
        parent::__construct($class);
        
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function createAddress()
    {
        $class = $this->getClass();
        return new $class;
    }
    
    /**
     * {@inheritdoc}
     */
    public function persistAddress(AddressInterface $address)
    {
        $this->entityManager->persist($address);
        $this->entityManager->flush();
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeAddress(AddressInterface $address)
    {
        $this->entityManager->remove($address);
        $this->entityManager->flush();
    }
    
    /**
     * {@inheritdoc}
     */
    public function findAddress($id)
    {
        return $this->repository->find($id);
    }
    
    /**
     * {@inheritdoc}
     */
    public function findAddressBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
    
    /**
     * {@inheritdoc}
     */
    public function findAddresses()
    {
        return $this->repository->findAll();
    }
    
    /**
     * {@inheritdoc}
     */
    public function findAddressesBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }
    
    /**
     * {@inheritdoc}
     */
    public function createPaginator()
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from($this->class, 'a');
            
        return new Pagerfanta(new DoctrineORMAdapter($queryBuilder->getQuery()));
    }
}
