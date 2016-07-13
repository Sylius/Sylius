<?php

/*
 * This file is a part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\NumberGenerator;

use Doctrine\ORM\EntityManager;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderSequenceInterface;
use Sylius\Component\Resource\Factory\Factory;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class OrderNumberGenerator implements OrderNumberGeneratorInterface
{
    /**
     * @var EntityRepository
     */
    private $sequenceRepository;

    /**
     * @var Factory
     */
    private $sequenceFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;
    
    /**
     * @var int
     */
    private $startNumber;

    /**
     * @var int
     */
    private $numberLength;

    /**
     * @param EntityRepository $sequenceRepository
     * @param Factory $sequenceFactory
     * @param EntityManager $entityManager
     * @param int $startNumber
     * @param int $numberLength
     */
    public function __construct(
        EntityRepository $sequenceRepository,
        Factory $sequenceFactory,
        EntityManager $entityManager,
        $startNumber = 1,
        $numberLength = 9
    ) {
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
        $this->entityManager = $entityManager;
        $this->startNumber = $startNumber;
        $this->numberLength = $numberLength;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $sequence = $this->getSequence();
        
        $number = $this->generateNumber($sequence->getIndex());
        $sequence->incrementIndex();
        
        return $number;
    }

    /**
     * @param int $index
     *
     * @return string
     */
    private function generateNumber($index)
    {
        $number = $this->startNumber + $index;

        return str_pad($number, $this->numberLength, 0, STR_PAD_LEFT);
    }

    /**
     * @return OrderSequenceInterface
     */
    private function getSequence()
    {
        $sequence = $this->sequenceRepository->findOneBy([]);

        if (null === $sequence) {
            $sequence = $this->sequenceFactory->createNew();
            $this->entityManager->persist($sequence);
        }

        return $sequence;
    }
}
