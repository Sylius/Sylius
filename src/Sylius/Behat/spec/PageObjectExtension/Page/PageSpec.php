<?php

namespace spec\Sylius\Behat\PageObjectExtension\Page;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Sylius\Behat\PageObjectExtension\Page\Page
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\PageObjectExtension\Page\Page');
    }

    function it_implements_TODO_interface()
    {
        $this->shouldImplement('Sylius\Behat\PageObjectExtension\Page\PageInterface');
    }
}
