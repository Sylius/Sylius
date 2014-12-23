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
 * Number generator interface.
 *
 * The implementation should generate next number.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface GeneratorInterface
{
    const CASE_UPPER = 'uppercase';
    const CASE_LOWER = 'lowercase';
    const CASE_MIXED = 'mixed-case';

    const FORMAT_DIGITS = 0;
    const FORMAT_STRING = 1;
    const FORMAT_MIXED  = 2;

    /**
     * Generate and apply next available number for given subject.
     *
     * @param SequenceSubjectInterface $subject
     * @param SequenceInterface        $sequence
     */
    public function generate(SequenceSubjectInterface $subject, SequenceInterface $sequence);
}
