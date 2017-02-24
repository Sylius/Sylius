<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Order\NumberGenerator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderSequenceInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class SequentialOrderNumberGenerator implements OrderNumberGeneratorInterface
{
    /**
     * @var RepositoryInterface
     */
    private $sequenceRepository;

    /**
     * @var FactoryInterface
     */
    private $sequenceFactory;

    /**
     * @var EntityManagerInterface
     */
    private $sequenceManager;

    /**
     * @var int
     */
    private $startNumber;

    /**
     * @var int
     */
    private $numberLength;

    /**
     * @param RepositoryInterface $sequenceRepository
     * @param FactoryInterface $sequenceFactory
     * @param EntityManagerInterface $sequenceManager
     * @param int $startNumber
     * @param int $numberLength
     */
    public function __construct(
        RepositoryInterface $sequenceRepository,
        FactoryInterface $sequenceFactory,
        EntityManagerInterface $sequenceManager,
        $startNumber = 1,
        $numberLength = 9
    ) {
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
        $this->sequenceManager = $sequenceManager;
        $this->startNumber = $startNumber;
        $this->numberLength = $numberLength;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(OrderInterface $order)
    {
        $sequence = $this->getSequence();

        $this->sequenceManager->lock($sequence, LockMode::OPTIMISTIC, $sequence->getVersion());
        
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
        /** @var OrderSequenceInterface $sequence */
        $sequence = $this->sequenceRepository->findOneBy([]);

        if (null !== $sequence) {
            return $sequence;
        }

        $sequence = $this->sequenceFactory->createNew();
        $this->sequenceManager->persist($sequence);

        return $sequence;
    }
}
