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

use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Sylius\Component\Sequence\Repository\HashSubjectRepositoryInterface;

/**
 * Hash order number generator.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class HashGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * @var HashSubjectRepositoryInterface
     */
    protected $subjectRepository;

    public function __construct(HashSubjectRepositoryInterface $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * {@inheritdoc}
     * This generates a 3 by 7 by 7 digit number (much like amazon's order identifier)
     * e.g. 105-3958356-3707476
     */
    protected function generateNumber($index, SequenceSubjectInterface $order)
    {
        do {
            $number = $this->generateSegment(3).'-'.$this->generateSegment(7).'-'.$this->generateSegment(7);
        } while ($this->subjectRepository->isNumberUsed($number));

        return $number;
    }

    /**
     * Generates a randomized segment
     *
     * @param  int    $length
     *
     * @return string Random characters
     */
    protected function generateSegment($length)
    {
        return substr(str_pad(mt_rand(), $length, 0, STR_PAD_LEFT), 0, $length);
    }
}
