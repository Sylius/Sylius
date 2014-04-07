<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SequenceBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Sequence\Manager\SequenceManagerInterface;
use Sylius\Component\Sequence\Repository\SequenceRepositoryInterface;
use Doctrine\ORM\NoResultException;

/**
 * Manager class for Sequence, provide method to increment and retrieve sequence indexes
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class SequenceManager implements SequenceManagerInterface
{
    /**
     * @var SequenceRepositoryInterface
     */
    protected $repository;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @param SequenceRepositoryInterface $repository
     * @param ObjectManager               $manager
     */
    public function __construct(SequenceRepositoryInterface $repository, ObjectManager $manager)
    {
        $this->repository = $repository;
        $this->manager    = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function setNextIndex($type)
    {
        $this->repository->incrementIndex($type);

        try {
            $index = $this->repository->getLastIndex($type);
        } catch (NoResultException $e) {
            $this->createSequence($type);
            $index = 0;
        }

        return $index;
    }

    /**
     * Create, persist and flush a new Sequence
     *
     * @param string $type
     */
    protected function createSequence($type)
    {
        $className = $this->repository->getClassName();

        $sequence = new $className($type);

        $this->manager->persist($sequence);
        $this->manager->flush($sequence);
    }
}
