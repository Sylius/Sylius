<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\User\Canonicalizer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Canonicalizer\Canonicalizer;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CanonicalizerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Canonicalizer::class);
    }

    function it_implements_canonicalizer_interface()
    {
        $this->shouldImplement(CanonicalizerInterface::class);
    }

    function it_converts_strings_to_lower_case()
    {
        $testString = 'tEsTsTrInG';
        $this->canonicalize($testString)->shouldReturn('teststring');
    }
}
