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

namespace spec\Sylius\Component\Resource\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class RandomnessGeneratorSpec extends ObjectBehavior
{
    function it_implements_randomness_generator_interface(): void
    {
        $this->shouldImplement(RandomnessGeneratorInterface::class);
    }

    function it_generates_random_uri_safe_string_of_length(): void
    {
        $length = 9;

        $this->generateUriSafeString($length)->shouldBeString();
        $this->generateUriSafeString($length)->shouldHaveLength($length);
    }

    function it_generates_random_numeric_string_of_length(): void
    {
        $length = 12;

        $this->generateNumeric($length)->shouldBeString();
        $this->generateNumeric($length)->shouldBeNumeric();
        $this->generateNumeric($length)->shouldHaveLength($length);
    }

    function it_generates_random_int_in_range(): void
    {
        $min = 12;
        $max = 2000000;

        $this->generateInt($min, $max)->shouldBeInt();
        $this->generateInt($min, $max)->shouldBeInRange($min, $max);
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers(): array
    {
        return [
            'haveLength' => function($subject, $length) {
                return $length === strlen($subject);
            },
            'beInRange' => function($subject, $min, $max) {
                return $subject >= $min && $subject <= $max;
            }
        ];
    }
}
