<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace %namespace%;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class %name% extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\%subject%::class);
    }
}
