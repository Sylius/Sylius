<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\User\Security\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Sylius\Component\User\Security\Checker\UniquenessCheckerInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;

final class UniqueTokenGeneratorSpec extends ObjectBehavior
{
    function let(RandomnessGeneratorInterface $generator, UniquenessCheckerInterface $checker): void
    {
        $this->beConstructedWith($generator, $checker, 12);
    }

    function it_implements_generator_interface(): void
    {
        $this->shouldImplement(GeneratorInterface::class);
    }

    function it_throws_invalid_argument_exception_on_instantiation_with_an_out_of_range_length(
        RandomnessGeneratorInterface $generator,
        UniquenessCheckerInterface $checker
    ): void {
        $this->beConstructedWith($generator, $checker, -1);
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
        $this->beConstructedWith($generator, $checker, 0);
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_generates_tokens_with_length_stated_on_instantiation(
        RandomnessGeneratorInterface $generator,
        UniquenessCheckerInterface $checker
    ): void {
        $token = 'vanquishable';

        $generator->generateUriSafeString(12)->willReturn($token);
        $checker->isUnique($token)->willReturn(true);

        $this->generate()->shouldHaveLength(12);
    }

    function it_generates_string_tokens(RandomnessGeneratorInterface $generator, UniquenessCheckerInterface $checker): void
    {
        $token = 'vanquishable';

        $generator->generateUriSafeString(12)->willReturn($token);
        $checker->isUnique($token)->willReturn(true);

        $this->generate()->shouldBeString();
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers(): array
    {
        return [
            'haveLength' => function ($subject, $key) {
                return $key === strlen($subject);
            },
        ];
    }
}
