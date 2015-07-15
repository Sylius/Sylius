<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\User\Security\Generator;

use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class TokenGeneratorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\User\Security\Generator\TokenGenerator');
    }

    public function it_implements_generator_interface()
    {
        $this->shouldImplement('Sylius\Component\User\Security\Generator\GeneratorInterface');
    }

    public function it_throws_exception_when_not_int_given()
    {
        $this->shouldThrow('InvalidArgumentException')->during('generate', array('string'));
        $this->shouldThrow('InvalidArgumentException')->during('generate', array(new \stdClass()));
        $this->shouldThrow('InvalidArgumentException')->during('generate', array(1.2));
        $this->shouldThrow('InvalidArgumentException')->during('generate', array(array()));
    }

    public function it_throws_exception_when_incorrect_length_provided()
    {
        $this->shouldThrow('InvalidArgumentException')->during('generate', array(-1));
        $this->shouldThrow('InvalidArgumentException')->during('generate', array(0));
        $this->shouldThrow('InvalidArgumentException')->during('generate', array(41));
        $this->shouldThrow('InvalidArgumentException')->during('generate', array(123));
    }

    public function it_generates_random_string()
    {
        $this->generate(1)->shouldBeString();
        $this->generate(7)->shouldBeString();
        $this->generate(16)->shouldBeString();
        $this->generate(40)->shouldBeString();
    }

    public function it_generates_random_string_with_given_length()
    {
        $this->generate(1)->shouldHaveLength(1);
        $this->generate(7)->shouldHaveLength(7);
        $this->generate(16)->shouldHaveLength(16);
        $this->generate(40)->shouldHaveLength(40);
    }

    public function getMatchers()
    {
        return array(
            'haveLength' => function ($subject, $key) {
                return $key === strlen($subject);
            },
        );
    }
}
