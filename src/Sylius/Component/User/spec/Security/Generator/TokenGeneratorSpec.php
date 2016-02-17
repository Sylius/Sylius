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
use Sylius\Component\User\Security\Generator\GeneratorInterface;

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
        $this->shouldImplement(GeneratorInterface::class);
    }

    public function it_throws_exception_when_not_int_given()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', ['string']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [new \stdClass()]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [1.2]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [[]]);
    }

    public function it_throws_exception_when_incorrect_length_provided()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [-1]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [0]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [41]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [123]);
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
        return [
            'haveLength' => function ($subject, $key) {
                return $key === strlen($subject);
            },
        ];
    }
}
