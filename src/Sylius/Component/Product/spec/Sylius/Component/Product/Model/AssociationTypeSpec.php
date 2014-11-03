<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AssociationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Cross sell');
    }

    function it_has_name()
    {
        $this->getName()->shouldBe('Cross sell');
    }

    function it_needs_to_have_mutable_name_sadly()
    {
        $this->setName('Changed name')->shouldBe($this);
        $this->getName()->shouldBe('Changed name');
    }

    function it_cannot_be_created_with_empty_name()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('__construct', array(''));
        $this->shouldThrow('\InvalidArgumentException')->during('__construct', array('   '));
        $this->shouldThrow('\InvalidArgumentException')->during('__construct', array("\n"));
        $this->shouldThrow('\InvalidArgumentException')->during('__construct', array("\t"));
        $this->shouldThrow('\InvalidArgumentException')->during('__construct', array(null));
    }

    function it_does_not_allow_to_change_name_to_empty_one()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('setName', array(''));
        $this->shouldThrow('\InvalidArgumentException')->during('setName', array('   '));
        $this->shouldThrow('\InvalidArgumentException')->during('setName', array("\n"));
        $this->shouldThrow('\InvalidArgumentException')->during('setName', array("\t"));
        $this->shouldThrow('\InvalidArgumentException')->during('setName', array(null));
    }
}
