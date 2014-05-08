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
 * Number generator interface.
 * The implementation should generate next order number.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface GeneratorInterface
{
    /**
     * Generate and apply next available number for given subject.
     *
     * @param SequenceSubjectInterface $subject
     * @param SequenceInterface        $sequence
     */
    public function generate(SequenceSubjectInterface $subject, SequenceInterface $sequence);
}
