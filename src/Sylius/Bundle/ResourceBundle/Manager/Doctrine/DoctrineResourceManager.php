<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Manager\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Manager\ResourceManager;
use Sylius\Bundle\ResourceBundle\ResourceInterface;

/**
 * Doctrine resource manager class.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DoctrineResourceManager extends ResourceManager implements DoctrineResourceManagerInterface
{
    protected $objectManager;
    protected $objectRepository;

    public function __construct(ObjectManager $objectManager, $class)
    {
        $this->objectManager = $objectManager;
        $this->objectRepository = $objectManager->getRepository($class);

        parent::__construct($class);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(ResourceInterface $resource, $commit = true)
    {
        $this->objectManager->persist($resource);

        if ($commit) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ResourceInterface $resource, $commit = true)
    {
        $this->objectManager->remove($resource);

        if ($commit) {
            $this->objectManager->flush();
        }
    }

    public function find($id)
    {
        return $this->objectRepository->find($id);
    }

    public function findOneBy(array $criteria)
    {
        return $this->objectRepository->findOneBy($criteria);
    }

    public function findAll()
    {
        return $this->objectRepository->findAll();
    }

    public function findBy(array $criteria)
    {
        return $this->objectRepository->findBy($criteria);
    }

    public function getObjectManager()
    {
        return $this->objectManager;
    }

    public function getObjectRepository()
    {
        return $this->objectRepository;
    }
}
