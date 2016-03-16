<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Repository;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FailsafeThemeRepository implements ThemeRepositoryInterface
{
    /**
     * @var ThemeRepositoryInterface
     */
    private $unstableRepository;

    /**
     * @var ThemeRepositoryInterface
     */
    private $fallbackRepository;

    /**
     * @param ThemeRepositoryInterface $unstableRepository
     * @param ThemeRepositoryInterface $fallbackRepository
     */
    public function __construct(ThemeRepositoryInterface $unstableRepository, ThemeRepositoryInterface $fallbackRepository)
    {
        $this->unstableRepository = $unstableRepository;
        $this->fallbackRepository = $fallbackRepository;
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        try {
            return call_user_func_array([$this->unstableRepository, $method], $arguments);
        } catch (\Exception $exception) {
            return call_user_func_array([$this->fallbackRepository, $method], $arguments);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        try {
            return $this->unstableRepository->find($id);
        } catch (\Exception $exception) {
            return $this->fallbackRepository->find($id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        try {
            return $this->unstableRepository->findAll();
        } catch (\Exception $exception) {
            return $this->fallbackRepository->findAll();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        try {
            return $this->unstableRepository->findBy($criteria, $orderBy, $limit, $offset);
        } catch (\Exception $exception) {
            return $this->fallbackRepository->findBy($criteria, $orderBy, $limit, $offset);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria)
    {
        try {
            return $this->unstableRepository->findOneBy($criteria);
        } catch (\Exception $exception) {
            return $this->fallbackRepository->findOneBy($criteria);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        try {
            return $this->unstableRepository->getClassName();
        } catch (\Exception $exception) {
            return $this->fallbackRepository->getClassName();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createPaginator(array $criteria = null, array $orderBy = null)
    {
        try {
            return $this->unstableRepository->createPaginator($criteria, $orderBy);
        } catch (\Exception $exception) {
            return $this->fallbackRepository->createPaginator($criteria, $orderBy);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(ResourceInterface $resource)
    {
        try {
            return $this->unstableRepository->add($resource);
        } catch (\Exception $exception) {
            return $this->fallbackRepository->add($resource);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ResourceInterface $resource)
    {
        try {
            return $this->unstableRepository->remove($resource);
        } catch (\Exception $exception) {
            return $this->fallbackRepository->remove($resource);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        try {
            return $this->unstableRepository->findOneByName($name);
        } catch (\Exception $exception) {
            return $this->fallbackRepository->findOneByName($name);
        }
    }
}
