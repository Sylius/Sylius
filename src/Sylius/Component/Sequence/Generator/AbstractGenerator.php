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

use Sylius\Component\Sequence\Model\SequenceInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;

/**
 * Base number generator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * @var string
     */
    protected $formatCase = self::CASE_MIXED;

    /**
     * @var string
     */
    protected $sequenceFormat = self::FORMAT_MIXED;

    /**
     * {@inheritdoc}
     */
    public function generate(SequenceSubjectInterface $subject, SequenceInterface $sequence)
    {
        if (null !== $subject->getNumber()) {
            return;
        }

        $subject->setNumber($this->generateNumber($sequence->getIndex(), $subject));

        $sequence->incrementIndex();
    }

    /**
     * @param string $format
     */
    public function setFormatCase($format)
    {
        $this->formatCase = $format;
    }

    /**
     * @param mixed $format
     */
    public function setSequenceFormat($format)
    {
        $this->sequenceFormat = $format;
    }

    /**
     * Generate the number
     *
     * @param int                      $index
     * @param SequenceSubjectInterface $subject
     *
     * @return string
     */
    abstract protected function generateNumber($index, SequenceSubjectInterface $subject);
}
