<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Sequence\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SequenceSpec extends ObjectBehavior
{
    function it_increments_index()
    {
        $this->setIndex(1);

        $this->incrementIndex();

        $this->getIndex()->shouldReturn(2);
    }

    function it_has_default_index()
    {
        $this->getIndex()->shouldReturn(1);
    }
}
