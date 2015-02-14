<?php

namespace spec\Sylius\Bundle\ResourceBundle\Form\Guesser;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FieldGuesserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Guesser\FieldGuesser');
    }
}
