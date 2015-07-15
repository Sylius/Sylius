<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence;

class SyliusSequenceEvents
{
    const PRE_GENERATE = 'sylius.sequence.%s.pre_generate';
    const POST_GENERATE = 'sylius.sequence.%s.post_generate';
}
