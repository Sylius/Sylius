<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Order\NumberGenerator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Component\Core\Model\OrderSequenceInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class SequentialOrderNumberGenerator implements OrderNumberGeneratorInterface
{
    public function __construct(
        private RepositoryInterface $sequenceRepository,
        private FactoryInterface $sequenceFactory,
        private EntityManagerInterface $sequenceManager,
        private int $startNumber = 1,
        private int $numberLength = 9,
    ) {
    }

    public function generate(OrderInterface $order): string
    {
        $sequence = $this->getSequence();

        $this->sequenceManager->lock($sequence, LockMode::OPTIMISTIC, $sequence->getVersion());

        $number = $this->generateNumber($sequence->getIndex());
        $sequence->incrementIndex();

        return $number;
    }

    private function generateNumber(int $index): string
    {
        $number = $this->startNumber + $index;

        return str_pad((string) $number, $this->numberLength, '0', \STR_PAD_LEFT);
    }

    private function getSequence(): OrderSequenceInterface
    {
        /** @var OrderSequenceInterface|null $sequence */
        $sequence = $this->sequenceRepository->findOneBy([]);

        if (null !== $sequence) {
            return $sequence;
        }

        /** @var OrderSequenceInterface $sequence */
        $sequence = $this->sequenceFactory->createNew();
        $this->sequenceManager->persist($sequence);

        return $sequence;
    }
}
