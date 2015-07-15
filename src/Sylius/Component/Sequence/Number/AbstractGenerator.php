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

use Sylius\Component\Sequence\Model\SequenceInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;

/**
 * Default order number generator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
abstract class AbstractGenerator implements GeneratorInterface
{
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
     * Generate the number
     *
     * @param int                      $index
     * @param SequenceSubjectInterface $subject
     *
     * @return string
     */
    abstract protected function generateNumber($index, SequenceSubjectInterface $subject);
}
