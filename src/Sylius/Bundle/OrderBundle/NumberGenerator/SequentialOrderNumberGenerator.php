<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\NumberGenerator;

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
     * @param int $startNumber
     * @param int $numberLength
     */
    public function __construct(
        RepositoryInterface $sequenceRepository,
        FactoryInterface $sequenceFactory,
        $startNumber = 1,
        $numberLength = 9
    ) {
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
        $this->startNumber = $startNumber;
        $this->numberLength = $numberLength;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(OrderInterface $order)
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
        /** @var OrderSequenceInterface $sequence */
        $sequence = $this->sequenceRepository->findOneBy([]);

        if (null === $sequence) {
            $sequence = $this->sequenceFactory->createNew();
            $this->sequenceRepository->add($sequence);
        }

        return $sequence;
    }
}
