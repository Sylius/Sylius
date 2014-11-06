<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Number;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Sequence\Model\SequenceInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;

/**
 * Sequential number generator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class SequentialGenerator implements GeneratorInterface
{
    /**
     * @var array
     */
    protected $sequences = array();

    /**
     * @var RepositoryInterface
     */
    protected $sequenceRepository;

    /**
     * @var ObjectManager
     */
    protected $sequenceManager;

    /**
     * Order number max length.
     *
     * @var integer
     */
    protected $numberLength;

    /**
     * Start number
     *
     * @var integer
     */
    protected $startNumber;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $sequenceRepository
     * @param ObjectManager $sequenceManager
     * @param integer $numberLength
     * @param integer $startNumber
     */
    public function __construct(
        RepositoryInterface $sequenceRepository,
        ObjectManager $sequenceManager,
        $numberLength = 9,
        $startNumber = 1
    ) {
        $this->sequenceRepository = $sequenceRepository;
        $this->manager = $sequenceManager;
        $this->numberLength = $numberLength;
        $this->startNumber  = $startNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(SequenceSubjectInterface $subject)
    {
        if (null !== $subject->getNumber()) {
            return;
        }

        $sequence = $this->getSequence($subject->getSequenceType());
        $number = str_pad($sequence->getIndex(), $this->numberLength, 0, STR_PAD_LEFT);

        $subject->setNumber($number);
        $sequence->incrementIndex();
    }

    /**
     * @param string $type
     * @return SequenceInterface object
     */
    protected function getSequence($type)
    {
        if (isset($this->sequences[$type])) {
            return $this->sequences[$type];
        }

        $sequence = $this->sequenceRepository->findOneBy(array('type' => $type));

        if (null === $sequence) {
            $sequence = $this->sequenceRepository->createNew()->setType($type)->setIndex($this->startNumber);
            $this->sequenceManager->persist($sequence);
        }

        return $this->sequences[$type] = $sequence;
    }
}
