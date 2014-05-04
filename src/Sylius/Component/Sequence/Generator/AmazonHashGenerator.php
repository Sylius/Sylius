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

/**
 * "Amazon" like hash order number generator.
 *
 * This generates a 3 by 7 by 7 digit number (much like amazon's order identifier)
 * e.g. 105-3958356-3707476
 *
 * @author Myke Hines <myke@webhines.com>
 */
class AmazonHashGenerator extends HashGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    protected function generateNumber($index, SequenceSubjectInterface $subject)
    {
        do {
            $number = $this->generateSegment(3) . '-' . $this->generateSegment(7) . '-' . $this->generateSegment(7);
        } while ($this->subjectRepository->isNumberUsed($number));

        return $number;
    }
}
