<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Generator;

use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Sylius\Component\Sequence\Provider\SequenceProviderInterface;

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
     * @var SequenceProviderInterface
     */
    protected $sequenceProvider;

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
     * @param SequenceProviderInterface $sequenceProvider
     * @param integer $numberLength
     * @param integer $startNumber
     */
    public function __construct(SequenceProviderInterface $sequenceProvider, $numberLength = 9, $startNumber = 1)
    {
        $this->sequenceProvider = $sequenceProvider;
        $this->numberLength = $numberLength;
        $this->startNumber = $startNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(SequenceSubjectInterface $subject)
    {
        if (null !== $subject->getNumber()) {
            return;
        }

        $sequence = $this->sequenceProvider->getSequence($subject->getSequenceType());
        $number = $this->formatNumber($sequence->getIndex() + $this->startNumber);

        $subject->setNumber($number);
        $sequence->incrementIndex();
    }

    /**
     * @param integer $number
     * @return string
     */
    protected function formatNumber($number)
    {
        return str_pad($number, $this->numberLength, 0, STR_PAD_LEFT);
    }
}
