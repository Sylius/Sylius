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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnitOfWork;
use Sylius\Bundle\SalesBundle\Model\StatusInterface;
use Sylius\Bundle\SalesBundle\Model\StatusManager as BaseStatusManager;

/**
 * Order status model for Doctrine ORM driver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StatusManager extends BaseStatusManager
{
    /**
     * Entity manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Status entity repository.
     *
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param EntityManager $entityManager
     * @param string        $class
     */
    public function __construct(EntityManager $entityManager, $class)
    {
        parent::__construct($class);

        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($this->getClass());
    }

    /**
     * {@inheritdoc}
     */
    public function createStatus()
    {
        $class = $this->getClass();
        return new $class;
    }

    /**
     * {@inheritdoc}
     */
    public function persistStatus(StatusInterface $status)
    {
        $this->entityManager->persist($status);
        $this->refreshStatusPosition($status);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeStatus(StatusInterface $status)
    {
        $this->entityManager->remove($status);
        $this->refreshStatusPosition($status);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function moveStatusUp(StatusInterface $status)
    {
        if (!$relatedStatus = $this->repository->findOneBy(array('position' => $status->getPosition() - 1))) {

            throw new \LogicException('Cannot move up top status');
        }
        $this->swapStatusPosition($status, $relatedStatus);
    }

    /**
     * {@inheritdoc}
     */
    public function moveStatusDown(StatusInterface $status)
    {
        if (!$relatedStatus = $this->repository->findOneBy(array('position' => $status->getPosition() + 1))) {

            throw new \LogicException('Cannot move down bottom status');
        }
        $this->swapStatusPosition($status, $relatedStatus);
    }

    /**
     * {@inheritdoc}
     */
    public function findStatus($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findStatusBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findStatuses()
    {
        return $this->repository->createQueryBuilder('s')
            ->orderBy('s.position')
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findStatusesBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Swaps statuses positions.
     *
     * @param StatusInterface $a
     * @param StatusInterface $b
     */
    protected function swapStatusPosition(StatusInterface $a, StatusInterface $b)
    {
        $positionA = $a->getPosition();
        $positionB = $b->getPosition();

        $a->setPosition($positionB);
        $b->setPosition($positionA);

        $this->entityManager->persist($a);
        $this->entityManager->persist($b);
        $this->entityManager->flush();
    }

    /**
     * Refresh status position.
     *
     * @param StatusInterface $category
     */
    protected function refreshStatusPosition(StatusInterface $status)
    {
        if (UnitOfWork::STATE_REMOVED === $this->entityManager->getUnitOfWork()->getEntityState($status)) {
            $this->repository->createQueryBuilder('s')
                ->update()
                ->set('s.position', 's.position - 1')
                ->where('s.position > :position')
                ->setParameter('position', $status->getPosition())
                ->getQuery()
                ->execute()
            ;
        } elseif (0 === $status->getPosition()) {
            $maxPosition = $this->getMaxPosition();
            $status->setPosition($maxPosition + 1);
        }
    }

    /**
     * Returns max position.
     *
     * @return integer
     */
    protected function getMaxPosition()
    {
        return $this->repository->createQueryBuilder('s')
            ->select('MAX(s.position)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
