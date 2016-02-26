<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Context\Ui\ThemeContext;

/**
 * @mixin ThemeContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\ThemeContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }
}
