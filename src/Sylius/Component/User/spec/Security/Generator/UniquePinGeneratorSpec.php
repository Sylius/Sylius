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
use Prophecy\Argument;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Sylius\Component\User\Security\Checker\UniquenessCheckerInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Sylius\Component\User\Security\Generator\UniquePinGenerator;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class UniquePinGeneratorSpec extends ObjectBehavior
{
    function let(RandomnessGeneratorInterface $generator, UniquenessCheckerInterface $checker)
    {
        $this->beConstructedWith($generator, $checker, 6);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UniquePinGenerator::class);
    }

    function it_implements_generator_interface()
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_throws_invalid_argument_exception_on_instantiation_with_non_integer_length(
        RandomnessGeneratorInterface $generator,
        UniquenessCheckerInterface $checker
    ) {
        $this->beConstructedWith($generator, $checker, 'a string');
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
        $this->beConstructedWith($generator, $checker, '8');
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
        $this->beConstructedWith($generator, $checker, []);
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
        $this->beConstructedWith($generator, $checker, new \StdClass());
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_invalid_argument_exception_on_instantiation_with_an_out_of_range_length(
        RandomnessGeneratorInterface $generator,
        UniquenessCheckerInterface $checker
    ) {
        $this->beConstructedWith($generator, $checker, -1);
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
        $this->beConstructedWith($generator, $checker, 0);
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
        $this->beConstructedWith($generator, $checker, 10);
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_generates_pins_with_length_stated_on_instantiation(
        RandomnessGeneratorInterface $generator,
        UniquenessCheckerInterface $checker
    ) {
        $pin = '001100';

        $generator->generateNumeric(6)->willReturn($pin);
        $checker->isUnique($pin)->willReturn(true);

        $this->generate()->shouldHaveLength(6);
    }

    function it_generates_string_pins(RandomnessGeneratorInterface $generator, UniquenessCheckerInterface $checker)
    {
        $pin = '636363';

        $generator->generateNumeric(6)->willReturn($pin);
        $checker->isUnique($pin)->willReturn(true);

        $this->generate()->shouldBeString();
    }

    function it_generates_numeric_pins(RandomnessGeneratorInterface $generator, UniquenessCheckerInterface $checker)
    {
        $pin = '424242';

        $generator->generateNumeric(6)->willReturn($pin);
        $checker->isUnique($pin)->willReturn(true);

        $this->generate()->shouldBeNumeric();
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers()
    {
        return [
            'haveLength' => function ($subject, $key) {
                return $key === strlen($subject);
            },
        ];
    }
}
